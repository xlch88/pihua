cat <<'EOF' >.git/hooks/post-merge
#!/bin/sh
set -e

REPO_ROOT="$(git rev-parse --show-toplevel)"

bash "$REPO_ROOT/builder/build.sh"
EOF
chmod +x .git/hooks/post-merge
