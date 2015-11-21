# Pak PHP

### Made by: Nadir Latif (nadir@pakjiddat.com, http://pakjiddat.com)

Depends on: PHP Utilities Framework: General purpose collection of classes (http://www.phpclasses.org/package/9388-PHP-General-purpose-collection-of-classes.html)

### The Pak Php framework is a PHP micro framework. The goal of the framework is to assist in the development of php applications. It implements front controller design pattern and is useful for developing scripts and backend applications

**The main features of the Pak Php framework are:

1. **It provides support for developing WordPress plugins**.
2. **It provides a template engine that allows complete separation of php and html code.** The template engine supports pull based as well as push based desgin pattern. It means the view can be rendered by multiple controllers or a single controller
3. **It provides database abstraction objects for MySQL and Memcache.** These objects simplify access to MySQL and Memcache databases
4. **It provides support for unit and functional testing.** It allows automation of functional testing.
5. **It implements dependency inversion principle.** It means the high level classes can use lower level classes without any dependency. For example you can implement your own Test class derived from the base Testing class. The main application class will use your implementation of the Testing class
6. **It provides support for session based authentication and http authentication.**
7. **The application provides a configuration file that contains all the information about the project**
8. **It allows an application to have multiple modules.** For example you can have several modules such as the example project. Each module has its own configuration file
9. **The example folder implements a sample application based on the Pak Php framework.** It can be accessed using following url: http://localhost/?option=save
10. **The Php Php framework implements a flexible MVC desgin pattern.** A template function is defined as a function that is rendered using html templates. A controller function is defined as a function that is not rendered using templates. The framework can call your application functions in following ways:
   a. **Dont specify any application url mapping.** The framework will use the application object defined in configuration file. It will convert the action parameter given in url and convert it to camel case. e.g if the application is called with this url: http://localhost/?option=save then framework will call the function HandleIndex in the application object. The application object is declared in configuration file using following code: $this->user_configuration['required_frameworks']['application']['class_name']     = 'Example\Example';
   b. **Specify the application url mapping using template functions.** This allows the application to use templates. The user has to give the names of all the template files to be used for the given controller action. For each template file the user has to give the name of the object and function that will provide the parameters for rendering the template file. For example base_page.html is the base page template. It can include other templates or tags. If it includes more templates then the configuration file has to mention these templates. For each template the configuration file has to give the callback that will provide the parameters for the template file. See the Configuration.php file in example folder for details. The application can also specify a presentation object that will provide data to a controller function. The code: $this->user_configuration['general']['use_presentation']         = true; enables the use of a presentation object. The functions of this object are automatically called by the framework. They provide data to the controller object functions. This further separates the presentation logic from the view logic
   c. **Specify the application url mapping using controller functions.** This option allows the application to handle a url using a given callback. The data returned by this function is simply displayed back to the user. If the data is an array then it is json encoded and then displayed
11. **It provides utility classes that can be used by your application.** These classes provide access to general purpose utility functions
12. **All classes are auto loaded, so no need to include any classes.** Objects are only created when they are requested by the application              