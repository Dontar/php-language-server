<?php



$obj = new TestClass();
$obj->testMethod();
echo $obj->testProperty;
TestClass::staticTestMethod();
echo TestClass::$staticTestProperty;
echo TestClass::TEST_CLASS_CONST;
test_function();

$var = 123;
echo $var;

/**
 * Aute duis elit reprehenderit tempor cillum proident anim laborum eu laboris reprehenderit ea incididunt.
 *
 * @param TestClass $param Adipisicing non non cillum sint incididunt cillum enim mollit.
 * @return TestClass
 */
function whatever(TestClass $param): TestClass {
    echo $param;
}

$fn = function() use ($var) {
    echo $var;
};

echo TEST_CONST;
