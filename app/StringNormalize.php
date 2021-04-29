<?php

namespace App;

class StringNormalize {

	/**
	 * @param string $string
	 * @param array $replacements
	 * @return string
	 */
	public static function replace(?string $string, array $replacements): string{
		return strtr(mb_strtolower($string), $replacements);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public static function lettersUkrToRus(?string $string): string{
		$replacements = [
			'і' => 'и',
			'й' => 'и',
			'ї' => 'и',
			'ы' => 'и',
			'є' => 'е',
			'ё' => 'е',
			'э' => 'е',
			'ґ' => 'г',
			'ъ' => 'ь',
			'\'' => 'ь',
			'"' => '',
			'-' => '',
		];

		return preg_replace('/\s+/', ' ', self::replace($string, $replacements));
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public static function layoutToRus(?string $string): string{
		$convertTable = [
			// english to russian
			'`' => 'ё',
			'~' => 'ё',
			'q' => 'й',
			'w' => 'ц',
			'e' => 'у',
			'r' => 'к',
			't' => 'е',
			'y' => 'н',
			'u' => 'г',
			'i' => 'ш',
			'o' => 'щ',
			'p' => 'з',
			'[' => 'х',
			']' => 'х',
			'{' => 'ъ',
			'}' => 'ъ',
			'a' => 'ф',
			's' => 'ы',
			'd' => 'в',
			'f' => 'а',
			'g' => 'п',
			'h' => 'р',
			'j' => 'о',
			'k' => 'л',
			'l' => 'д',
			';' => 'ж',
			':' => 'ж',
			'\'' => 'э',
			'"' => 'э',
			'z' => 'я',
			'x' => 'ч',
			'c' => 'с',
			'v' => 'м',
			'b' => 'и',
			'n' => 'т',
			'm' => 'ь',
			',' => 'б',
			'<' => 'б',
			'.' => 'ю',
			'>' => 'ю',
			'/' => '.',
			'?' => ',',

			// ukrainian to russian
			'ї' => 'ъ',
			'є' => 'э',
			'і' => 'ы',
		//	'ґ' => '', //??????
		];

		return self::replace($string, $convertTable);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public static function stringToFuzzy(?string $string): string{
		$alphabetReplacements = [
			// глухие согласные
			'п' => 'б',
			'ф' => 'в',
			'к' => 'г',
			'т' => 'д',
			'с' => 'з',
			'ш' => 'ж',

			// гласные
			'а' => 'а',
			'е' => 'е',
			'ё' => 'е',
			'и' => 'е',
			'о' => 'а',
			'э' => 'е',

			// игнорируем
			'ъ' => '',
			'ь' => '',

			// гласные, без изменений
			'у' => 'у',
			'ы' => 'ы',
			'ю' => 'ю',
			'я' => 'я',

			// согласные, без изменений
			'б' => 'б',
			'в' => 'в',
			'г' => 'г',
			'д' => 'д',
			'ж' => 'ж',
			'з' => 'з',
			'й' => 'й',
			'л' => 'л',
			'м' => 'м',
			'н' => 'н',
			'р' => 'р',
			'х' => 'х',
			'ц' => 'ц',
			'ч' => 'ч',
			'щ' => 'щ',
		];

		// ^\w used intentionally to include all unicode letters
		$clearString = preg_replace(/** @lang RegExp */ '/[^\w ]|_/u', '', $string);
		$removedDuplicatedLetters = preg_replace('/(.)\1+/umi','$1',$clearString);

		return self::replace($removedDuplicatedLetters, $alphabetReplacements);
	}

}
