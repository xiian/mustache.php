<?php

require_once '../Mustache.php';

class MustacheImplicitContextsTest extends PHPUnit_Framework_TestCase {

	public function testImplicitContexts() {
		$data = array(
			array('variable' => 'foo'),
			array('variable' => 'bar'),
			array('variable' => 'baz'),
		);

		$m1 = new Mustache();
		$this->assertEquals("foo\nbar\nbaz", $m1->render('{{variable}}', $data));

		$m2 = new Mustache('<{{variable}}>', $data);
		$this->assertEquals("<foo>\n<bar>\n<baz>", $m2->render());
	}

	public function testMultipleRenderCallsWithImplicitContexts() {
		$data = array(
			array('variable' => 'foo'),
			array('variable' => 'bar'),
			array('variable' => 'baz'),
		);

		$m = new Mustache('{{variable}}', $data);
		$this->assertEquals($m->render(), $m->render());
	}

	public function testDeeplyNestedImplicitContexts() {
		$data = array(
			array('foo' => array('bar' => array('baz' => 'win'))),
			array('foo' => array('bar' => array('baz' => 'win'))),
			array('foo' => array('bar' => array('baz' => 'win'))),
		);

		$m2 = new Mustache('{{#foo}}{{#bar}}{{baz}}{{/bar}}{{/foo}}', $data);
		$this->assertEquals("win\nwin\nwin", $m2->render());

		$m2 = new Mustache();
		$this->assertEquals("win\nwin\nwin", $m2->render('{{#foo}}{{#bar}}{{baz}}{{/bar}}{{/foo}}', $data));
	}

	public function testImplicitContextObjects() {
		$obj = new StdClass();
		$obj->name = '~';
		$data = array(
			array('obj' => $obj),
			array('obj' => $obj),
			array('obj' => $obj),
		);

		$m = new Mustache('{{#obj}}{{name}}{{/obj}}', $data);
		$this->assertEquals("~\n~\n~", $m->render());
	}

	public function testArrayObjectImplicitContext() {
		$data = array(
			array('variable' => 'foo'),
			array('variable' => 'bar'),
			array('variable' => 'baz'),
		);
		$obj = new ArrayObject($data);

		$m1 = new Mustache();
		$this->assertEquals("foo\nbar\nbaz", $m1->render('{{variable}}', $obj));

		$m2 = new Mustache('<{{variable}}>', $obj);
		$this->assertEquals("<foo>\n<bar>\n<baz>", $m2->render());
	}

	public function testIterableObjectImplicitContext() {
		$data = new ImplicitContextIterableObject(array(
			array('variable' => 'foo'),
			array('variable' => 'bar'),
			array('variable' => 'baz'),
		));

		$m1 = new Mustache();
		$this->assertEquals("foo\nbar\nbaz", $m1->render('{{variable}}', $data));

		$m2 = new Mustache('<{{variable}}>', $data);
		$this->assertEquals("<foo>\n<bar>\n<baz>", $m2->render());
	}

	public function testIterableMustacheImplicitContext() {
		$this->markTestSkipped('This whole idea is still a bit... undefined at the moment.');

		$m = new ImplicitContextIterableMustache();
		$this->assertEquals("foo\nbar\nbaz", $m->render('{{variable}}'));
	}
}

class ImplicitContextIterableObject implements IteratorAggregate {
	public function __construct(array $data) {
		$this->iterator = new ArrayIterator($data);
	}

	public function getIterator() {
		return $this->iterator;
	}
}

class ImplicitContextIterableMustache extends Mustache implements IteratorAggregate {

	protected $data = array(
		array('variable' => 'foo'),
		array('variable' => 'bar'),
		array('variable' => 'baz'),
	);

	public function getIterator() {
		return new ArrayIterator($data);
	}
}