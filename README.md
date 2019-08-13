# bignum-php
Steven S. Skiena's bignum.c to PHP.

Implements large integer arithmetic: addition, subtraction, multiplication, and division.

The original C program appears in Steve's book: ["Programming Challenges: The Programming Contest Training Manual"](http://www.amazon.com/exec/obidos/ASIN/0387001638/thealgorithmrepo/)
by Steven Skiena and Miguel Revilla, Springer-Verlag, New York 2003.

See Steve and Miguel's [website](http://www.programming-challenges.com) for additional information.

Bruce's release contains:

 * README.md - this file
 * php-bignum.php
 * test.php
 * MIT.LICENSE

Better testing and better documentation may follow.

MIT license.

New News
--------

 * Started using PHPUnit

Old News
--------

 * Added base_to_bignum
 * Added bignum_to_base
 * Added clear_memo
 * test file demos conversion of decimal to various bases and back
 * Added power_modulus
 * Added power_bignum
 * Added memoization (not nearly as obvious an improvement as in js-bignum)
 * PHP already has GMP and BCMath, but done for the fact that I can.		