cat <<'EOF' >.git/hooks/post-merge
#!/bin/sh
set -e

REPO_ROOT="$(git rev-parse --show-toplevel)"

"$REPO_ROOT/builder/build.sh"
EOF
