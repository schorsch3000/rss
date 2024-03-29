.PHONY: dev setup deploy assets

dev: setup
	test -f config.yaml || cp config.dist.yaml config.yaml
	cd public && php -S 0.0.0.0:8888 index-dev.php

setup: vendor assets

assets:
	php minify.php

vendor: composer.json composer.lock
	composer -n install
	touch vendor

deploy:
	ssh dicky@rss.shnbk.de "cd domains/rss.shnbk.de/app && git pull && make setup"
