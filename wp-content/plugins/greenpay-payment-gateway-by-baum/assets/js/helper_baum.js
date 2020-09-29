/**
** Card Format
**/
const card = formData => ({
	card: {
		cardHolder: formData.cardName,
		expirationDate: {
			month: Number(formData.cardExpirationMonth.month),
			year: formData.cardExpirationMonth.year,
		},
		cardNumber: formData.cardNumber,
		cvc: formData.cardExpirationCVV,
		nickname: formData.nickname || 'Baum Payments'
	}
});

// const cardTokenizeRequest = (session, cardData) => ({
// 	session,
// 	ld: cardData.result,
// 	lk: cardData.keyPairs
// });

/**
** Encrypt data
**/
const getAESKeyPairs = () => {
	let iv;
	const key = [];
	for (let k = 0; k < 16; k += 1) {
		key.push(Math.floor(Math.random() * 255));
	}
	for (let k = 0; k < 16; k += 1) {
		iv = Math.floor(Math.random() * 255);
	}
	return ({ k: key, s: iv });
};

const encryptData = (publicKey, data) => {
	const rsa = new JSEncrypt();
	rsa.setPublicKey(publicKey);

	const pair = getAESKeyPairs();

	const textBytes = aesjs.utils.utf8.toBytes(data);
	const aesCtr = new aesjs.ModeOfOperation.ctr(pair.k, new aesjs.Counter(pair.s));

	const encryptedBytes = aesCtr.encrypt(textBytes);
	const encryptedHex = aesjs.utils.hex.fromBytes(encryptedBytes);

	return {
		result: encryptedHex,
		keyPairs: rsa.encrypt(JSON.stringify(pair))
	};
};

/**
** Init
**/
function formatCard(cardDataForm) {
	return JSON.stringify(card(cardDataForm));
}
// {"card":{"cardHolder":"Alex Ramos","expirationDate":{"month":6,"year":"25"},"cardNumber":"4111111111111111","cvc":"123","nickname":"Baum Pagos"}}
// {"card":{"cardHolder":"Visa","expirationDate":{"month":5,"year":"12"},"cardNumber":"4111 1111 1111 1111","cvc":"112","nickname":"Delfino Payments"}}

function encryptCard(publicKey, data) {
	return encryptData(publicKey, data);
}
 
// Only a demo
function demoKeyPairs() {
	const rsa = new JSEncrypt();
	const pair = getAESKeyPairs();
	return rsa.encrypt(JSON.stringify(pair));
}

// formatCard({
// 	cardName: "Visa",
// 	cardExpirationMonth: {
// 		month: 05
// 	},
// 	cardNumber: "4111 1111 1111 1111",
// 	cardExpirationCVV: "112"
// });