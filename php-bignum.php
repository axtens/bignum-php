<?php
$maxdigits = 100;
define("PLUS",1);		/* positive sign bit */
define("MINUS",-1);		/* negative sign bit */

$MEMO = array();

class BIGNUM {
	public $signbit;
	public $lastdigit;
	public $digits;
	public function __construct($n = 100) 
	{
		$this->signbit = 1;
		$this->lastdigit = -1;
		$this->digits = array_fill(0,$n,0);
	}
	public function toString()
	{
		$r = '';
		if ($this->signbit == MINUS) 
		{
			$r = "-";
		}
		for ($i = $this->lastdigit; $i>=0; $i--)
		{
			$r = $r . (string)$this->digits[$i];
		}
		return $r;	
	}
}

class BIGNUMLIB {
	private $maxdigits;
	public function __construct($n = 100) 
	{
		global $maxdigits;
		$maxdigits = $n;
		$this->Bignum = new BIGNUM($maxdigits);
	}

	public function set_precision( $n )
	{
		global $maxdigits;
		$maxdigits = $n;
		return;
	}

	public function clear_memo()
	{
		global $MEMO;
		$MEMO = array();
		return;
	}
	
	public function print_bignum($n)
	{
		if ($n->signbit == MINUS) 
		{
			echo("-");
		}
		for ($i = $n->lastdigit; $i>=0; $i--)
			echo($n->digits[$i]);

		echo("\n");
	}

	public function bignum_to_string($n)
	{
		$r = '';
		if ($n->signbit == MINUS) 
		{
			$r = "-";
		}
		for ($i = $n->lastdigit; $i>=0; $i--)
			$r = $r . (string)$n->digits[$i];

		return $r;
	}

	public function int_to_bignum($s, &$n)
	{
		global $maxdigits;

		if ($s >= 0) 
		{
			$n->signbit = PLUS;
		}
		else
		{
			$n->signbit = MINUS;
		}
		
		// for ($i=0; $i<$maxdigits; $i++) 
		// {	
			// $n->digits[$i] = 0;
		// }

		$n->digits = array_fill(0,$maxdigits,0);
		
		$n->lastdigit = -1;

		$t = abs($s);

		while ($t > 0) {
			$n->lastdigit ++;
			$n->digits[ $n->lastdigit ] = ($t % 10);
			$t = floor($t / 10);
		}

		if ($s == 0) 
		{
			$n->lastdigit = 0;
		}
		return $n;
	}

	public function string_to_bignum( $s, &$b )
	{
		global $maxdigits;
		
		$this->initialize_bignum($b);
		if ( $s == '' ) 
		{
			return;
		}

		if (substr($s, 0, 1) === '-') 
		{
			$t = 1;
			$b->signbit = MINUS;
			$s = substr( $s, 1 );
		} 
		else 
		{
			$t = 0;
			$b->signbit = PLUS;
		}

		$b->lastdigit = -1;

		$b->digits = array_fill(0,$maxdigits,0);
		
		for ($i = strlen($s) - 1; $i >= $t; $i-- )
		{
			$b->lastdigit++;
			$b->digits[$b->lastdigit] = (int)(substr($s, $i, 1));
		}
		return $b;
	}


	public function initialize_bignum($n)
	{
		$this->int_to_bignum(0,$n);
	}

	public function add_bignum($a, $b, &$c)
	{
		$key = $a->toString() . "+" . $b->toString();
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}

		$this->initialize_bignum($c);

		if ($a->signbit == $b->signbit) 
		{
			$c->signbit = $a->signbit;
		}
		else 
		{
			if ($a->signbit == MINUS) 
			{
				$a->signbit = PLUS;
				$this->subtract_bignum($b,$a,$c);
				$a->signbit = MINUS;
			} 
			else 
			{
				$b->signbit = PLUS;
				$this->subtract_bignum($a,$b,$c);
				$b->signbit = MINUS;
			}
			return;
		}

		$c->lastdigit = max($a->lastdigit,$b->lastdigit)+1;
		$carry = 0;

		for ($i=0; $i<=($c->lastdigit); $i++) {
			// echo "i=" . $i . "\n";
			// echo "a digits length= " . count($a,1). "\n";
			// echo "b digits length= " . count($b). "\n";
			// echo "a=" . $a->digits[$i] . "\n";
			// echo "b=" . $b->digits[$i] . "\n";
			$value = $carry + $a->digits[$i] + $b->digits[$i];
			$c->digits[$i] = $value % 10;
			$carry = floor( $value / 10);
		}

		$this->zero_justify($c);
		$MEMO[$key] = clone $c;
		return;
	}

	public function subtract_bignum($a, $b, &$c)
	{
		$key = $a->toString() . "-" . $b->toString();
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}

		$this->initialize_bignum($c);

		if (($a->signbit == MINUS) || ($b->signbit == MINUS)) {
			$b->signbit = -1 * $b->signbit;
			$this->add_bignum($a,$b,$c);
			$b->signbit = -1 * $b->signbit;
			return;
			}

		if ($this->compare_bignum($a,$b) == PLUS) {
			$this->subtract_bignum($b,$a,$c);
			$c->signbit = MINUS;
			return;
		}

		$c->lastdigit = max($a->lastdigit,$b->lastdigit);
		$borrow = 0;

		for ($i=0; $i<=($c->lastdigit); $i++) 
		{
			$v = ($a->digits[$i] - $borrow - $b->digits[$i]);
			if ($a->digits[$i] > 0)
			{
				$borrow = 0;
			}
			if ($v < 0) 
			{
				$v = $v + 10;
				$borrow = 1;
			}

			$c->digits[$i] = $v % 10;
		}
		$this->zero_justify($c);
		$MEMO[$key] = clone $c;

		return;
	}

	public function compare_bignum($a, $b)
	{
		$key = $a->toString() . "$" . $b->toString();
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}
		
		// int i;				/* counter */
		$res = 0;
		if (($a->signbit == MINUS) && ($b->signbit == PLUS)) 
		{
			$res = (PLUS);
		}
		elseif (($a->signbit == PLUS) && ($b->signbit == MINUS)) 
		{
			$res = (MINUS);
		}
		elseif ($b->lastdigit > $a->lastdigit) 
		{
			$res = (PLUS * $a->signbit);
		} 
		elseif ($a->lastdigit > $b->lastdigit) 
		{
			$res = (MINUS * $a->signbit);
		}
		else 
		{
			for ($i = $a->lastdigit; $i>=0; $i--) 
			{
				if ($a->digits[$i] > $b->digits[$i]) 
				{
					$res = (MINUS * $a->signbit);
					break;
				}
				if ($b->digits[$i] > $a->digits[$i]) 
				{
					$res = (PLUS * $a->signbit);
					break;			
				}
			}
		}
		$MEMO[$key] = $res;
		return $res;
	}

	function zero_justify(&$n)
	{
		while (($n->lastdigit > 0) && ($n->digits[ $n->lastdigit ] == 0))
		{
			$n->lastdigit --;
		}
		if (($n->lastdigit == 0) && ($n->digits[0] == 0))
		{
			$n->signbit = PLUS;	/* hack to avoid -0 */
		}
		return;
	}


	function digit_shift(&$n, $d)		/* multiply n by 10^d */
	{
		if (($n->lastdigit == 0) && ($n->digits[0] == 0)) 
		{
			return;
		}
		
		for ($i=$n->lastdigit; $i>=0; $i--)
		{
			$n->digits[$i+$d] = $n->digits[$i];
		}
		
		for ($i=0; $i<$d; $i++) 
		{
			$n->digits[$i] = 0;
		}
		
		$n->lastdigit = $n->lastdigit + $d;
	}



	public function multiply_bignum($a, $b, &$c)
	{
		global $maxdigits;

		$key = $a->toString() . "*" . $b->toString();
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}
		
		$tmp = new BIGNUM($maxdigits);
		$row = new BIGNUM($maxdigits);

		$this->initialize_bignum($c);
		$this->initialize_bignum($row);
		$this->initialize_bignum($tmp);

		$row = clone $a;

		for ($i=0; $i<=$b->lastdigit; $i++) {
			for ($j=1; $j<=$b->digits[$i]; $j++) {
				$this->add_bignum($c,$row,$tmp);
				$c = clone $tmp;
			}
			$this->digit_shift($row,1);
		}

		$c->signbit = $a->signbit * $b->signbit;

		$this->zero_justify($c);
		$MEMO[$key] = clone $c;
		return;
	}


	public function divide_bignum($a, $b, &$c)
	{
		global $maxdigits;
	
		$key = $a->toString() . "/" . $b->toString();
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}


		$row = new BIGNUM($maxdigits);
		$tmp = new BIGNUM($maxdigits);
		
		$this->initialize_bignum($c);
		$this->initialize_bignum($row);
		$this->initialize_bignum($tmp);

		$c->signbit = $a->signbit * $b->signbit;

		$asign = $a->signbit;
		$bsign = $b->signbit;

		$a->signbit = PLUS;
		$b->signbit = PLUS;

		$this->initialize_bignum($row);
		$this->initialize_bignum($tmp);

		$c->lastdigit = $a->lastdigit;

		for ($i=$a->lastdigit; $i>=0; $i--) {
			$this->digit_shift($row,1);
			$row->digits[0] = $a->digits[$i];
			$c->digits[$i] = 0;
			while ($this->compare_bignum($row,$b) != PLUS) {
				$c->digits[$i]++;
				$this->subtract_bignum($row,$b,$tmp);
				$row = clone $tmp;
			}
		}

		$this->zero_justify($c);
		$MEMO[$key] = clone $c;

		$a->signbit = $asign;
		$b->signbit = $bsign;
		return;
	}

	public function power_bignum( $a, $n, &$c )
	{
		global $maxdigits;
	
		$key = $a->toString() . "^" . (string)$n;
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}

		$this->initialize_bignum($c);
		
		if ($n == 0)
		{
			$this->int_to_bignum(1,$c);
		}
		else
		{
			$c = clone $a;
			$res = new BIGNUM($maxdigits);
			$this->int_to_bignum(0,$res);
			
			for ( $i = 1; $i < $n; $i++ )
			{
				$this->multiply_bignum($a, $c, $res );
				$c = clone $res;
			}
		}
		$MEMO[$key] = clone $c;
		return;
	}

	public function modulus_bignum( $a, $b, &$c )
	{
		global $maxdigits;
		$key = $a->toString() . "%" . $b->toString();
		global $MEMO;
		if (in_array($key, $MEMO, true)) 
		{
			$c = $MEMO[$key];
			return;
		}

		$this->initialize_bignum($c);
		$div = new BIGNUM($maxdigits);
		$this->initialize_bignum($div);
		$mul = new BIGNUM($maxdigits);
		$this->initialize_bignum($mul);
		$sub = new BIGNUM($maxdigits);
		$this->initialize_bignum($sub);

		$comp = $this->compare_bignum( $a, $b );
		if ( $comp == MINUS ) {
			$this->divide_bignum( $a, $b, $div );
			$this->multiply_bignum($div, $b, $mul );
			$this->subtract_bignum($a, $mul, $sub );
			// echo $a->toString() . "/" . $b->toString() . "=" . $div->toString() . "\n";
			// echo $div->toString() . "*" . $b->toString() . "=" . $mul->toString() . "\n";
			// echo $a->toString() . "-" . $mul->toString() . "=" . $sub->toString() . "\n";
			$c = clone $sub;
		} elseif ($comp == PLUS) {
			$c = clone $a;
		} else {
			$this->initialize_bignum($c);
		}

		
		$MEMO[$key] = clone $c;
		return;
	}

	public function bignum_to_base( $a, &$c, $numset )
	{
		$base = new BIGNUM();
		$this->int_to_bignum(strlen($numset), $base);
		$zero = new BIGNUM();
		$this->int_to_bignum(0,$zero);
		$offs = new BIGNUM();
		$this->int_to_bignum(0,$offs);
		$temp = new BIGNUM();
		$this->int_to_bignum(0,$temp);
		
		$c = '';
		while ($this->compare_bignum($zero, $a) == PLUS)
		{
			$this->modulus_bignum($a, $base, $offs); 
			// echo $a->toString() . " % " . $base->toString() . " = " . $offs->toString() . "\n";
			$c = substr( $numset, (int)$offs->toString(), 1 ) . $c;
			// echo "substr " . $numset . " " . $offs->toString() . "=" . substr( $numset, (int)$offs->toString(), 1 ) . "\n"; 
			$this->divide_bignum($a, $base, $temp );
			$a = clone $temp;
		}		
		return;
	}

	public function base_to_bignum( $a, $numset, &$b )
	{
		$base = new BIGNUM();
		$this->int_to_bignum(strlen($numset), $base);
		$pwr = new BIGNUM();
		$this->int_to_bignum(0, $pwr);
		$ioc = new BIGNUM();
		$this->int_to_bignum(0, $ioc);
		$mul = new BIGNUM();
		$this->int_to_bignum(0, $mul);
		$sum = new BIGNUM();
		$this->int_to_bignum(0, $sum);
		$tmp = new BIGNUM();
		$this->int_to_bignum(0, $tmp);
		
		$this->initialize_bignum($b);
		
		$c = 0;
		$j = 0;
		// echo "strlen a = " . strlen($a) . "\n";
		for ( $i = (strlen($a) - 1); $i >= 0; $i-- ) 
		{
			$c = substr($a,$i,1);
			$this->power_bignum($base, $j, $pwr);
			$this->int_to_bignum(strpos($numset,$c),$ioc);
			$this->multiply_bignum($pwr,$ioc,$mul);
			$this->add_bignum($sum, $mul, $tmp);
			$sum = clone $tmp;
			// echo "c=" . $c . "\n";	
			// echo "pwr=" . $pwr->toString() . "\n";	
			// echo "ioc=" . $ioc->toString() . "\n";	
			// echo "mul=" . $mul->toString() . "\n";	
			// echo "tmp=" . $tmp->toString() . "\n";	
			// echo "sum=" . $sum->toString() . "\n";	
			$j++;
		}
		$b = clone $sum;
		return;
	}
}
