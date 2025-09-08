#!/usr/bin/perl
use warnings;
use strict;

# Platform detection
use constant IS_WINDOWS => ($^O =~ /MSWin32|cygwin/i);
use constant IS_LINUX => !IS_WINDOWS;

print "Platform detection test:\n";
print "IS_WINDOWS: " . IS_WINDOWS . "\n";
print "IS_LINUX: " . IS_LINUX . "\n";
print "Platform: " . (IS_WINDOWS ? "Windows/Cygwin" : "Linux") . "\n";

# Test conditional constants
use constant TEST_CONST => IS_LINUX ? "Linux value" : "Windows value";
print "TEST_CONST: " . TEST_CONST . "\n";

# Test conditional function
sub test_platform_function {
    if (IS_LINUX) {
        return "Linux-specific function";
    } else {
        return "Windows-specific function";  
    }
}

print "Platform function: " . test_platform_function() . "\n";