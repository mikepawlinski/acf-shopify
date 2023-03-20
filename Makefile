zip:
	rm -rf build ||:
	mkdir build
	zip -r "build/acf-shopify.zip" acf-shopify.php LICENSE vendor/*
