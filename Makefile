build: src
	php5-latest spress.phar site:build --env=prod
	cp -r build/* ~/domains/betablog.checkmyworking.com/html

clean:
	rm -r build
