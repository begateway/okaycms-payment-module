all:
	if [[ -e okaycms-payment-module.zip ]]; then rm okaycms-payment-module.zip; fi
	zip -r okaycms-payment-module.zip BeGateway -x "*/test/*" -x "*/.git/*" -x "*/examples/*"
