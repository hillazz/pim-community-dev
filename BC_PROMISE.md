Our Backwards Compatibility Promise
===================================

Ensuring smooth upgrades of your projects is very important for us. That's why
we promise you backwards compatibility (BC) for all minor Akeneo PIM releases.
You probably recognize this strategy as `Semantic Versioning`_. In short,
Semantic Versioning means that only major releases (such as 1.0, 2.0 etc.) are
allowed to break backwards compatibility. Minor releases (such as 1.5, 1.6 etc.)
may introduce new features, but must do so without breaking the existing API of
that release branch (2.x in the previous example).

In fact, almost every change that we make to the PIM can potentially break a customer's
project. For example, if we add a new method to a class, this will break a project
which extended this class and added the same method, but with a different
method signature.

Also, not every BC break has the same impact on project code. While some BC
breaks require you to make significant changes to your classes or your
architecture, others are fixed as easily as changing the name of a method.

That's why we created this page for you. The section "Using Akeneo PIM Code" will
tell you how you can ensure that your application won't break completely when
upgrading to a newer version of the same major release branch.

.. caution::

    This promise was introduced with Akeneo PIM 1.6 and does not apply to previous
    versions. We take a large inspiration of Symfony Backwards Compatibility Promise,
    hovewer, our product is still young, evolving a lot and we can't ensure such API
    stability on all our business code.

    We're starting this promise by a very limited amount of Interfaces and Classes. In
    your project or extension, you'll probably need other interfaces and classes on which
    we can't engage (for now).

    However, we commit to improve our Backward Compatibility Promise in upcoming versions,
    meaning we'll enlarge the coverage of the promise to make each minor version migration
    easier than the previous one.

Using Akeneo PIM Code
---------------------

If you are using Akeneo PIM in your extensions or projects, the following guidelines will help
you to ensure smooth upgrades to all future minor releases of your Akeneo PIM version.

Using our Interfaces
~~~~~~~~~~~~~~~~~~~~

Main Interfaces shipped with Akeneo PIM are tagged with `@api`, and can be used in type hints.
You can also call any of the methods that they declare. We guarantee that we won't break code that
sticks to these rules.

.. caution::

    All other Interfaces must be considered as tagged with ``@internal`` and are deserved to internal
    PIM usage, such Interfaces should not be used or implemented in your own code.

If you implement an `@api` interface, we promise that we won't ever break your code.

The following table explains in detail which use cases are covered by our backwards compatibility promise:

+-----------------------------------------------+-----------------------------+
| Use Case                                      | Backwards Compatibility     |
+===============================================+=============================+
| **If you...**                                 | **Then we guarantee BC...** |
+-----------------------------------------------+-----------------------------+
| Type hint against the interface               | Yes                         |
+-----------------------------------------------+-----------------------------+
| Call a method                                 | Yes                         |
+-----------------------------------------------+-----------------------------+
| **If you implement the interface and...**     | **Then we guarantee BC...** |
+-----------------------------------------------+-----------------------------+
| Implement a method                            | Yes                         |
+-----------------------------------------------+-----------------------------+
| Add an argument to an implemented method      | No (TODO; Yes in SF)        |
+-----------------------------------------------+-----------------------------+
| Add a default value to an argument            | No (TODO; Yes in SF)        |
+-----------------------------------------------+-----------------------------+

Using our Classes
~~~~~~~~~~~~~~~~~

Main classes shipped with Akeneo PIM are tagged with `@api`, these classes may be instantiated
and accessed through their public methods and properties.

.. caution::

    All other Classes must be considered as tagged with ``@internal`` and are deserved to internal
    PIM use only, such Classes should not be accessed by your own code.

To be on the safe side, check the following table to know which use cases are
covered by our backwards compatibility promise:

+-----------------------------------------------+-----------------------------+
| Use Case                                      | Backwards Compatibility     |
+===============================================+=============================+
| **If you...**                                 | **Then we guarantee BC...** |
+-----------------------------------------------+-----------------------------+
| Type hint against the class                   | Yes                         |
+-----------------------------------------------+-----------------------------+
| Create a new instance                         | Yes (TODO: construct breaks?|
+-----------------------------------------------+-----------------------------+
| Extend the class                              | Yes                         |
+-----------------------------------------------+-----------------------------+
| Access a public property                      | Yes                         |
+-----------------------------------------------+-----------------------------+
| Call a public method                          | Yes                         |
+-----------------------------------------------+-----------------------------+
| **If you extend the class and...**            | **Then we guarantee BC...** |
+-----------------------------------------------+-----------------------------+
| Access a protected property                   | Yes ?                       |
+-----------------------------------------------+-----------------------------+
| Call a protected method                       | Yes ?                       |
+-----------------------------------------------+-----------------------------+
| Override a public property                    | Yes                         |
+-----------------------------------------------+-----------------------------+
| Override a protected property                 | Yes ?                       |
+-----------------------------------------------+-----------------------------+
| Override a public method                      | Yes                         |
+-----------------------------------------------+-----------------------------+
| Override a protected method                   | Yes ?                       |
+-----------------------------------------------+-----------------------------+
| Add a new property                            | No                          |
+-----------------------------------------------+-----------------------------+
| Add a new method                              | No                          |
+-----------------------------------------------+-----------------------------+
| Add an argument to an overridden method       | No (TODO; Yes in SF)        |
+-----------------------------------------------+-----------------------------+
| Add a default value to an argument            | No (TODO; Yes in SF)        |
+-----------------------------------------------+-----------------------------+
| Call a private method (via Reflection)        | No                          |
+-----------------------------------------------+-----------------------------+
| Access a private property (via Reflection)    | No                          |
+-----------------------------------------------+-----------------------------+

???TODO??
About extends, it really depends on several factors, for instance, for most of our classes it would be
very complicated but for classes implementing Pattern Method, you may not have the choice.

I would also prefer push project code to use composition over inheritance, to do so, using final to forbid the inheritance
 
Another strategy to be able to engage on protected method is defining uncallable methods as private, in the project
history we always preferred using protected to offer a maximum of extensibility but this makes the promise on the API
even harder.

So, depending on the previous engagement on interfaces / classes I'd put different interfaces / classes as @api. 

IMVHO, To summarize,
More we restrict possibilities in previous promise arrays, more we can add @api interfaces / classes.
More we offer possibilities (override protected method for instance), less we can add @api interfaces / classes.

???END TODO??

.. _Semantic Versioning: http://semver.org/
