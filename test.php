$words = ["abcd", "abdc", "bca", "bca", "afz"];

foreach ($words as $word) {
	if (sort($word)) {
		echo $word. "Sıralı";
	}else{
		echo $word. "Sırasız";
	}
}
