BUNDLE_GEMFILE=./Gemfile
bundle install
bundle exec jekyll build --source ../ --destination ../.tmp/dist
