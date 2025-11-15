set -eux

REPO_ROOT="$(git rev-parse --show-toplevel)"
cd "$REPO_ROOT/builder"

BUNDLE_GEMFILE=./Gemfile
mkdir -p ../.tmp/
bundle exec jekyll build --source ../ --destination ../.tmp/dist

mkdir -p /website/dark495.cn/public_new
rsync -av ../.tmp/dist/ /website/dark495.cn/public_new/
rm -rf /website/dark495.cn/public_old
mv /website/dark495.cn/public /website/dark495.cn/public_old
mv /website/dark495.cn/public_new /website/dark495.cn/public
rm -rf ../.tmp/
