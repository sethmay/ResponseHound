ResponseHound
=============

About
-----

ResponseHound is an open source extension to [PHPUnit](http://www.phpunit.de/) 
released under simplified BSD license. The software is designed to:

* Quickly test data-based server responses
* Validate expected JSON data
* Verify the contract between client requests and server responses
* Embed into an already existing standard for testing (PHPUnit)
* Be easy to use and embed in your existing test structure.

Installation
------------

The current version of ResponseHound is hosted in the git repository on [GitHub](https://github.com). 
You may either download the project directly here:

	https://github.com/sethmay/ResponseHound/archives/master

or using Git:

	git clone git://github.com/sethmay/ResponseHound.git

You will need to have [PHPUnit](http://www.phpunit.de/) installed and working properly in order for
ResponseHound to run. Make sure that your [PEAR](http://pear.php.net) is in your include path.

How It Works
------------

ResponseHound works by

* Sending HTTP based requests to a web server
* Capturing the response
* Parsing the response into a useable form
* Executing tests against the returned data based on you definitions

To do this, you will make a basic request at the beginning of your test. This will include any 
parameters that need to be passed into you server. Once the data has been retrieved (and automatically 
parsed), you are free to start writing tests against the structure and content of the data.

Examples
--------

### Test Status

The first basic example will assume that  we will query a web server, which will give us a basic
status response:

	{
	    "status": "ok"
	}

We know what we expect the server to respond. We will now setup a test that will verify this. We 
start by creating a new ResponseHound testcase with a new test.

	class StatusTest extends ResponseHound_JSONTestCase
	{
	    public function test_getList_GET ()
	    {
	        $this->setBaseURL("http://localhost/server.php");
	             ->send();
	
	        $this->checkValue("status", "", "string", array("value" => "ok"));
	    }
	}

In the test, the first thing that we did was configure our server request. Using "fluent" syntax 
(also known as code chaining), our request was sent out to the server. At this point, the request 
has been made and the server has responded. The data has also been parsed. It's time for our testing 
to begin.

To test an element, we use the "checkValue" method call.

In this case, we will test to see if there is an root element named "status" that is a string with a 
value of "ok". This is our most basic test.

### Test a List of Colors

Now lets try something a little more complex.

	{
	    "transactionId": 1,
	    "status": "ok",
	    "data":
	    {
	        "colors":
	        [
	            {"color": "red",     "value": "#f00"},
	            {"color": "green",   "value": "#0f0"},
	            {"color": "blue",    "value": "#00f"},
	            {"color": "cyan",    "value": "#0ff"},
	            {"color": "magenta", "value": "#f0f"},
	            {"color": "yellow",  "value": "#ff0"},
	            {"color": "black",   "value": "#000"}
	        ]
	    }
	}

This time we're expecting the server to send us a well formed response that includes data about a list 
of colors. Our request is going to be a bit more complex, though. Now we're talking to a MVC based
system that requires us to supply it with more info such as a controller and an action.

	class ColorTest extends ResponseHound_JSONTestCase
	{
	    public function test_getList ()
	    {
	        $this->setBaseURL("http://localhost/ResponseHound/Samples/server/jsonGetServer.php")
	             ->addRequestParam("transactionId", 1)
	             ->addRequestParam("target", "colors")
	             ->addRequestParam("action", "getList")
	             ->send();
	
	        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
	             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
	             ->checkValue("data"           ,""        ,"array")
	             ->checkValue("colors"         ,"data"    ,"array");
	
	
	        $loc = "data.colors";
	        $this->checkListValue("color"    ,$loc      ,"string")
	             ->checkListValue("value"    ,$loc      ,"string");
	    }
	}

The first thing to note is that we've added several new request parameters. By default, these are
all passed in the query string (GET). Alternatively, you can pass them as POST values by using 
"sendPost ()".  We're asked the server to use action "getList" on target/controller "colors".

We're also testing much more returned data. We have two arrays "data" and "colors". Also notice
that "colors" is not at the root level, but a child of "data". We've passed along a location value
to specify where to look for this element.

Our last two tests are used to evaluate a list of items. With data responses, we often times have 
large lists of data that are identical in structure, but vary in value. Using "checkListValue ()", 
we can easily verify that the entire lists matches our expectation. This method uses the same syntax 
as "checkValue ()". Notice that the location uses a period (".") to delimit node locations, so these 
lists items are at root-&gt;data-&gt;colors (specified using "data.colors").

To make these list tests even more powerful, we could specify that the list of items be a member of a 
specified set of data. To do this, we use the option "valueIn".

	$loc = "data.colors";
	$colors = array("grey","red","gold","green","blue","white","cyan","magenta","yellow","black");
	$hexValues = array("#fff","#55d","#f00","#c33","#0f0","#00f","#0ff","#f0f","#ff0","#000");
	
	$this->checkListValue("color"    ,$loc      ,"string" ,array("valueIn" => $colors))
	     ->checkListValue("value"    ,$loc      ,"string" ,array("valueIn" => $hexValues));

Finally, we can be even more explicit. We could dictate that the returned values be exactly equal to a 
specified list of values. To do this, we use the option "valueList". This defines a 1 to 1 match. If there 
is any variance, the test will fail.

	$loc = "data.colors";
	$colors = array("red","green","blue","cyan","magenta","yellow","black");
	$hexValues = array("#f00","#0f0","#00f","#0ff","#f0f","#ff0","#000");
	
	$this->checkListValue("color"    ,$loc      ,"string" ,array("valueList" => $colors))
	     ->checkListValue("value"    ,$loc      ,"string" ,array("valueList" => $hexValues));

Reference
---------

### Configuring a Request

	JSONTestCase::setBaseURL ( $url : string ) : JSONTestCase

Set a well formed URL that will be used to request data. Return allows for fluent syntax.

	JSONTestCase::addRequestParam ( $name : string, $value : mixed ) : JSONTestCase
	
Add a parameter to be used by the request against the server to retrieve data. The name and value will be used
as the key-value pair when sending the data. In a GET query string, this would look like: 
http://www.test.com/index.php?name1=value1. Return allows for fluent syntax.

	JSONTestCase::setRequestParams ( $params : array ) : JSONTestCase

Allows you to set all the request parameters at once with an array. The array must be an associative array.
Return allows for fluent syntax.

### Sending a Request

	JSONTestCase::send () : JSONTestCase
	
An alias of sendGetRequest (). Return allows for fluent syntax.

	JSONTestCase::sendGet () : JSONTestCase
	
An alias of sendGetRequest (). Return allows for fluent syntax.

	JSONTestCase::sendGetRequest () : JSONTestCase
	
Sends the request, indicating that it should use GET to submit parameter data. Return allows for fluent syntax.

	JSONTestCase::sendPost () : JSONTestCase

An alias of sendPostRequest (). Return allows for fluent syntax.

	JSONTestCase::sendPostRequest () : JSONTestCase
	
Sends the request, indicating that it should use POST to submit parameter data. Return allows for fluent syntax.

	
### Testing Response Data

	JSONTestCase::checkValue ( $name : string [, $location : string [, $type : string [, $options : array ]]]) : JSONTestCase
	
Used to test a specific value in the response data. Return allows for fluent syntax.

$name - Correspond to the name of the value. 

$location - Used to indicate where the data will exist in the tree. Each item in the location needs to be an array.
Use periods (".") to delimit the location. For example "data.myList" would indicate that the value to be tested 
exists in the array "myList" which is an element in the array "data" which is an element of the root node. The root node
does not need to be set, or can be indicated using an empty string ("").

$type - Used to test the type of the data value. All common PHP types can be used (string, int, float, array, etc), as 
well as object names.

$options - An array of key/value pairs that specify additional options to be used. See the option information below. 

	JSONTestCase::checkListValue ( $name : string [, $location : string [, $type : string [, $options : array ]]]) : JSONTestCase
	
Used to test a value that is present in a list. For instance, if a list of people were present, you could test
all of the "firstName" elements in the list without having to specify each individual element. Return allows for fluent syntax.

$name - Correspond to the name of the value. 

$location - Used to indicate where the list will exist in the tree. Each item in the location needs to be an array.
Use periods (".") to delimit the location. For example "data.myList" would indicate that the value to be tested 
exists in the array "myList" which is an element in the array "data" which is an element of the root node. The root node
does not need to be set, or can be indicated using an empty string ("").

$type - Used to test the type of the data value. All common PHP types can be used (string, int, float, array, etc), as 
well as object names.

$options - An array of key/value pairs that specify additional options to be used. See the option information below. 	

### Options for JSONTestCase::checkValue ()

	allowNull: boolean (false)
	
Indicates that the value can be null. If null, don't assertValue.

	data: array
	
An alternative dataset to test against.

	itemCount: int

Will test to verify that the array item has the specified number of values.

	mustExist: boolean (true)

Indicates that the item must exist. If false, having the item missing won't fail.

	value: mixed

Will test to verify that the item is equivalent to the value. This also works with arrays.

	valueIn: array

Test to see if the value is in the given array.

### Options for JSONTestCase::checkListValue ()

checkListValue () will take all the same options as checkValue () with the following addition:

	valueList: array

A supplied list of values to test. Each items in the group is tested against the matching indexed value.</li>

### Using Your Own Request/Response/Transport Objects

ResponseHound allows you to use your own customized utility objects. To do this, create a new object that implements
the appropriate interface. In your testcase setup (), you will then do the following:

	public function setup ()
	{
		parent::setup();
		
		$transport = new MyTransport();
		$response = new MyResponse ();
		$request = new MyRequest();
		
		$request->setTransport($transport)
				->setResponse($response);
				
		$this->setRequest($request);
	}
	
or more simply

	public function setup ()
	{
		parent::setup();
		
		$request = new MyRequest();
		$request->setTransport( new MyTransport() )
				->setResponse( new MyResponse() );
				
		$this->setRequest($request);
	}

Your testcase will now use the new object that you've build to do your request/response/ or transport.
