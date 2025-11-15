set -eux

cd "$(dirname "$0")/.."

git reset --hard
git clean -fd
git pull
