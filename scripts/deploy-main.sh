#!/usr/bin/bash

set -u

readonly REPOSITORY="/home/zauryx/web/sentriq.xytriq.com/public_html"
readonly BRANCH="main"
readonly LOG_FILE="${REPOSITORY}/storage/logs/deploy-main.log"
readonly GIT=(/usr/bin/git -c "safe.directory=${REPOSITORY}")

log() {
    local message="$1"

    /usr/bin/printf '[%s] %s\n' "$(/usr/bin/date --iso-8601=seconds)" "$message" >> "$LOG_FILE"
    /usr/bin/logger --tag sentriq-deploy -- "$message"
}

if ! cd "$REPOSITORY"; then
    log "ERROR: no se pudo entrar al repositorio."
    exit 1
fi

current_branch="$("${GIT[@]}" symbolic-ref --quiet --short HEAD 2>> "$LOG_FILE")" || {
    log "ERROR: el repositorio no está en una rama."
    exit 1
}

if [[ "$current_branch" != "$BRANCH" ]]; then
    log "ERROR: se esperaba la rama ${BRANCH}, pero está activa ${current_branch}."
    exit 1
fi

working_tree="$("${GIT[@]}" status --porcelain --untracked-files=all 2>> "$LOG_FILE")" || {
    log "ERROR: no se pudo revisar el estado del repositorio."
    exit 1
}

if [[ -n "$working_tree" ]]; then
    log "AVISO: actualización cancelada porque existen cambios locales."
    /usr/bin/printf '%s\n' "$working_tree" >> "$LOG_FILE"
    exit 1
fi

if ! "${GIT[@]}" fetch --quiet origin "$BRANCH" >> "$LOG_FILE" 2>&1; then
    log "ERROR: no se pudieron consultar los cambios de origin/${BRANCH}."
    exit 1
fi

local_commit="$("${GIT[@]}" rev-parse HEAD 2>> "$LOG_FILE")" || {
    log "ERROR: no se pudo identificar el commit local."
    exit 1
}

remote_commit="$("${GIT[@]}" rev-parse FETCH_HEAD 2>> "$LOG_FILE")" || {
    log "ERROR: no se pudo identificar el commit remoto."
    exit 1
}

if [[ "$local_commit" == "$remote_commit" ]]; then
    exit 0
fi

if "${GIT[@]}" merge-base --is-ancestor "$local_commit" "$remote_commit"; then
    if "${GIT[@]}" merge --ff-only --quiet "$remote_commit" >> "$LOG_FILE" 2>&1; then
        log "Actualizado origin/${BRANCH}: ${local_commit:0:7} -> ${remote_commit:0:7}."
        exit 0
    fi

    log "ERROR: falló la actualización por avance rápido."
    exit 1
fi

if "${GIT[@]}" merge-base --is-ancestor "$remote_commit" "$local_commit"; then
    log "AVISO: la rama local está por delante de origin/${BRANCH}; no se modificó."
    exit 0
fi

log "ERROR: la rama local y origin/${BRANCH} han divergido; se requiere intervención manual."
exit 1
