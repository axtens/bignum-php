<?php
require dirname(__FILE__) . "/php-bignum.php";

class BignumTest extends PHPUnit_Framework_TestCase
{
	public function testAdd()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->string_to_bignum('11111111111',$arg1);
		$this->assertEquals($arg1->toString(),'11111111111');
		$arg2 = new BIGNUM();
		$code->string_to_bignum('11111111111',$arg2);
		$this->assertEquals($arg2->toString(),'11111111111');
		$answ = new BIGNUM();
		$code->add_bignum($arg1, $arg2, $answ);
		$this->assertEquals($answ->toString(), '22222222222');
	}
	public function testSub()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->string_to_bignum('12345678900',$arg1);
		$this->assertEquals($arg1->toString(),'12345678900');
		$arg2 = new BIGNUM();
		$code->string_to_bignum('12345678900',$arg2);
		$this->assertEquals($arg2->toString(),'12345678900');
		$answ = new BIGNUM();
		$code->subtract_bignum($arg1, $arg2, $answ);
		$this->assertEquals($answ->toString(), '0');
	}
	public function testMul()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->string_to_bignum('12345678900',$arg1);
		$this->assertEquals($arg1->toString(),'12345678900');
		$arg2 = new BIGNUM();
		$code->string_to_bignum('12345678900',$arg2);
		$this->assertEquals($arg2->toString(),'12345678900');
		$answ = new BIGNUM();
		$code->multiply_bignum($arg1, $arg2, $answ);
		$this->assertEquals($answ->toString(), '152415787501905210000');
	}

	public function testDiv()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->string_to_bignum('152415787501905210000',$arg1);
		$this->assertEquals($arg1->toString(),'152415787501905210000');
		$arg2 = new BIGNUM();
		$code->string_to_bignum('32767',$arg2);
		$this->assertEquals($arg2->toString(),'32767');
		$answ = new BIGNUM();
		$code->divide_bignum($arg1, $arg2, $answ);
		$this->assertEquals($answ->toString(), '4651502655168468');
	}
	public function testMod()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->string_to_bignum('152415787501905210000',$arg1);
		$this->assertEquals($arg1->toString(),'152415787501905210000');
		$arg2 = new BIGNUM();
		$code->string_to_bignum('4651502655168468',$arg2);
		$this->assertEquals($arg2->toString(),'4651502655168468');
		$answ = new BIGNUM();
		$code->modulus_bignum($arg1, $arg2, $answ);
		$this->assertEquals($answ->toString(), '19044');
	}
	public function testPow()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->int_to_bignum(2,$arg1);
		$this->assertEquals($arg1->toString(),'2');
		$arg2 = 50;
		$answ = new BIGNUM();
		$code->power_bignum($arg1, $arg2, $answ);
		$this->assertEquals($answ->toString(), '1125899906842624');
	}
	public function testBignumToBase()
	{
		$code = new BIGNUMLIB();
		$arg1 = new BIGNUM();
		$code->string_to_bignum("269202023480197091579312830",$arg1);
		$this->assertEquals($arg1->toString(),'269202023480197091579312830');
		$dijits = '0123456789ABCDEF';
		$answ = '';
		$code->bignum_to_base($arg1, $answ, $dijits);
		$this->assertEquals($answ, 'DEADBEEF4ADEADCAFEBABE');
	}
	public function testBaseToBignum()
	{
		$code = new BIGNUMLIB();
		$arg1 = 'DEADBEEF4ADEADCAFEBABE';
		$dijits = '0123456789ABCDEF';
		$answ = new BIGNUM();
		$code->initialize_bignum($answ);
		$code->base_to_bignum($arg1, $dijits, $answ);
		$this->assertEquals($answ->toString(), '269202023480197091579312830');
	}
}
?>