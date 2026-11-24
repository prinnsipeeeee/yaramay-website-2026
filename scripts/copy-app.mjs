// Copies the invoice-record-system PHP app into dist/ so that a single
// `npm run build` produces one deployable artifact:
//   dist/index.html              → static marketing site (Vite build)
//   dist/invoice-record-system/  → PHP invoice app (this copy)
//
// Runtime-only state (sessions, logs, local scratch) is excluded;
// runtime dirs are recreated empty so the app boots cleanly on the server.

import { cpSync, mkdirSync, rmSync } from 'node:fs'
import { join, normalize } from 'node:path'

const SRC = 'invoice-record-system'
const DEST = join('dist', 'invoice-record-system')

// Exclusion rules, matched against the path relative to invoice-record-system/
const EXCLUDE = [
  /^data\/sessions\//, // live PHP session files
  /^data\/.*\.log$/i, // e.g. db_fallback.log
  /^\.git/, // safety net; folder has no nested .git since the merge
  /^scratch\//, // local scratch space
]

rmSync(DEST, { recursive: true, force: true })
cpSync(SRC, DEST, {
  recursive: true,
  filter: (src) => {
    const rel = normalize(src).replace(/\\/g, '/')
    const short = rel.startsWith(SRC + '/') ? rel.slice(SRC.length + 1) : ''
    return short === '' || !EXCLUDE.some((re) => re.test(short))
  },
})

// Recreate runtime dirs (sessions must exist and be writable on the host)
mkdirSync(join(DEST, 'data', 'sessions'), { recursive: true })

console.log(`[copy-app] ${SRC}/ -> ${DEST}/ (runtime state excluded)`)
