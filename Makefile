build: src
	echo ce64c22 > last-commit.txt
	php5-latest spress.phar site:build --env=prod
	cp -r build/* ~/domains/test.aperiodical.com/html/betablog

clean:
	rm -r build
