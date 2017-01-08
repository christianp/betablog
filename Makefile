build: src
	php5-latest spress.phar site:build --env=prod
	cp -r build/* ~/domains/test.aperiodical.com/html/betablog

clean:
	rm -r build
