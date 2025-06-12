<?php

namespace App\Helper;

class ConvertNumber
{
	/**
	 * Converte o valor de centavo para real com 2 casas decimais
	 *
	 * @param int $centValue
	 ** @author Lennon Cécere
	 */
	public static function centToReal(int $centValue = 0)
	{
		return substr(number_format(($centValue / 100), 3, '.', ''), 0, -1);
	}
}