#!/usr/bin/env python3
"""
FAQ RSS Validator
Validates FAQ.RSS for well-formed XML and site-specific constraints.
Checks for no CDATA, allowed title values, and proper HTML escaping.
"""

import sys
import xml.etree.ElementTree as ET
import re
from pathlib import Path

class FAQRSSValidator:
    def __init__(self, rss_file="FAQ.RSS"):
        self.rss_file = Path(rss_file)
        self.errors = []
        self.warnings = []
        self.allowed_titles = {
            "Config Files",
            "Startup Parameters", 
            "Troubleshooting",
            "Steam Workshop"
        }
        
    def validate_xml_structure(self):
        """Validate basic XML structure and parsing"""
        try:
            tree = ET.parse(self.rss_file)
            self.root = tree.getroot()
            
            if self.root.tag != 'rss':
                self.errors.append("Root element must be 'rss'")
                return False
                
            channel = self.root.find('channel')
            if channel is None:
                self.errors.append("Missing 'channel' element")
                return False
                
            self.channel = channel
            return True
            
        except ET.ParseError as e:
            self.errors.append(f"XML parsing error: {e}")
            return False
        except FileNotFoundError:
            self.errors.append(f"RSS file not found: {self.rss_file}")
            return False
        except Exception as e:
            self.errors.append(f"Unexpected error parsing XML: {e}")
            return False
            
    def check_no_cdata(self):
        """Check that there are no CDATA sections in the file"""
        try:
            with open(self.rss_file, 'r', encoding='utf-8') as f:
                content = f.read()
                
            if '<![CDATA[' in content:
                self.errors.append("CDATA sections are not allowed")
                # Find specific locations
                lines = content.split('\n')
                for i, line in enumerate(lines, 1):
                    if '<![CDATA[' in line:
                        self.errors.append(f"  CDATA found at line {i}")
                        
        except Exception as e:
            self.errors.append(f"Error reading file for CDATA check: {e}")
            
    def check_allowed_titles(self):
        """Check that all item titles are from allowed set"""
        items = self.channel.findall('item')
        
        for i, item in enumerate(items, 1):
            title_elem = item.find('title')
            if title_elem is None:
                self.errors.append(f"Item {i}: Missing title element")
                continue
                
            title = title_elem.text
            if title not in self.allowed_titles:
                self.errors.append(f"Item {i}: Invalid title '{title}'. Allowed: {', '.join(sorted(self.allowed_titles))}")
                
    def check_required_elements(self):
        """Check that each item has required elements"""
        items = self.channel.findall('item')
        
        for i, item in enumerate(items, 1):
            # Check for title
            if item.find('title') is None:
                self.errors.append(f"Item {i}: Missing title element")
                
            # Check for category
            category_elem = item.find('category')
            if category_elem is None:
                self.errors.append(f"Item {i}: Missing category element")
            elif not category_elem.text or not category_elem.text.strip():
                self.errors.append(f"Item {i}: Empty category")
                
            # Check for content:encoded (with namespace)
            content_elem = item.find('{http://purl.org/rss/1.0/modules/content/}encoded')
            if content_elem is None:
                self.errors.append(f"Item {i}: Missing content:encoded element")
            elif not content_elem.text or not content_elem.text.strip():
                self.warnings.append(f"Item {i}: Empty content")
                
    def check_html_escaping(self):
        """Check that HTML content is properly escaped"""
        items = self.channel.findall('item')
        
        for i, item in enumerate(items, 1):
            content_elem = item.find('{http://purl.org/rss/1.0/modules/content/}encoded')
            if content_elem is None or not content_elem.text:
                continue
                
            content = content_elem.text
            
            # Check for unescaped < and > (except for allowed tags)
            allowed_tags = ['br', 'strong', '/strong']
            
            # Find all < and > characters
            for match in re.finditer(r'<([^>]*)>', content):
                tag = match.group(1).strip()
                if tag not in allowed_tags and not tag.startswith('&lt;') and not tag.startswith('&gt;'):
                    # Check if it's properly escaped
                    if not tag.startswith('&') or not tag.endswith(';'):
                        self.errors.append(f"Item {i}: Unescaped HTML tag '<{tag}>'")
                        
            # Check for unescaped & that aren't part of entities
            unescaped_amp = re.findall(r'&(?![a-zA-Z0-9#]+;)', content)
            if unescaped_amp:
                self.errors.append(f"Item {i}: Unescaped ampersand(s) found")
                
    def check_alphabetical_order(self):
        """Check that categories are in alphabetical order"""
        items = self.channel.findall('item')
        categories = []
        
        for item in items:
            category_elem = item.find('category')
            title_elem = item.find('title')
            
            if category_elem is not None and title_elem is not None:
                categories.append((category_elem.text, title_elem.text))
                
        # Check if categories are sorted
        sorted_categories = sorted(categories, key=lambda x: (x[0], x[1]))
        
        if categories != sorted_categories:
            self.warnings.append("Items are not in alphabetical order by category then title")
            
    def check_duplicate_items(self):
        """Check for duplicate category/title combinations"""
        items = self.channel.findall('item')
        seen = set()
        
        for i, item in enumerate(items, 1):
            category_elem = item.find('category')
            title_elem = item.find('title')
            
            if category_elem is not None and title_elem is not None:
                key = (category_elem.text, title_elem.text)
                if key in seen:
                    self.errors.append(f"Item {i}: Duplicate category/title combination: {key}")
                seen.add(key)
                
    def check_content_formatting(self):
        """Check content formatting requirements"""
        items = self.channel.findall('item')
        
        for i, item in enumerate(items, 1):
            content_elem = item.find('{http://purl.org/rss/1.0/modules/content/}encoded')
            if content_elem is None or not content_elem.text:
                continue
                
            content = content_elem.text
            
            # Check for proper use of <br> instead of newlines
            if '\n' in content and '<br>' not in content:
                self.warnings.append(f"Item {i}: Consider using <br> tags instead of newlines")
                
            # Check for unescaped strong tags (should be &lt;strong&gt;)
            if '<strong>' in content and '&lt;strong&gt;' not in content:
                self.warnings.append(f"Item {i}: Use &lt;strong&gt; instead of <strong> tags")
                
    def generate_statistics(self):
        """Generate statistics about the RSS file"""
        items = self.channel.findall('item')
        categories = {}
        
        for item in items:
            category_elem = item.find('category')
            if category_elem is not None and category_elem.text:
                categories[category_elem.text] = categories.get(category_elem.text, 0) + 1
                
        print("\nStatistics:")
        print(f"Total items: {len(items)}")
        print(f"Total categories: {len(categories)}")
        
        if categories:
            print("\nItems per category:")
            for category in sorted(categories.keys()):
                print(f"  {category}: {categories[category]} items")
                
    def validate(self):
        """Run all validation checks"""
        print(f"Validating RSS file: {self.rss_file}")
        print("=" * 50)
        
        # Basic XML structure validation
        if not self.validate_xml_structure():
            return False
            
        # Run all validation checks
        self.check_no_cdata()
        self.check_allowed_titles()
        self.check_required_elements()
        self.check_html_escaping()
        self.check_alphabetical_order()
        self.check_duplicate_items()
        self.check_content_formatting()
        
        # Report results
        print(f"Found {len(self.errors)} errors and {len(self.warnings)} warnings")
        
        if self.errors:
            print(f"\n❌ ERRORS ({len(self.errors)}):")
            for error in self.errors:
                print(f"  • {error}")
                
        if self.warnings:
            print(f"\n⚠️  WARNINGS ({len(self.warnings)}):")
            for warning in self.warnings:
                print(f"  • {warning}")
                
        if not self.errors and not self.warnings:
            print("\n✅ All validation checks passed!")
            
        # Generate statistics
        self.generate_statistics()
        
        return len(self.errors) == 0
        
    def run(self):
        """Main execution method"""
        return self.validate()

def main():
    if len(sys.argv) > 1:
        rss_file = sys.argv[1]
    else:
        rss_file = "FAQ.RSS"
        
    validator = FAQRSSValidator(rss_file)
    success = validator.run()
    
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()