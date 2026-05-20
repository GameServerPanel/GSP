#!/usr/bin/env python3
"""
Quality Control and Linting for Generated Game Guides

Validates that generated guides meet all requirements:
- No forbidden provider mentions
- No HTML entity escaping
- All required sections present
- Sufficient startup parameters
- Proper <PLACEHOLDER> handling
"""

import os
import sys
import re
import json
from pathlib import Path

class GuideQualityControl:
    def __init__(self, md_dir="out/md", pdf_dir="out/pdfs"):
        self.md_dir = Path(md_dir)
        self.pdf_dir = Path(pdf_dir)
        
        # Forbidden terms that should not appear in output
        self.forbidden_terms = [
            'OGP', 'OpenGamePanel', 'LinuxGSM', 'Nitrado', 
            'GameServers.com', 'G-Portal', 'gameservers.com',
            'control panel', 'panel setup', 'OGP module'
        ]
        
        # Required sections that must be present
        self.required_sections = [
            '## Overview',
            '## System Requirements', 
            '## Ports & Networking',
            '## Startup Parameters',
            '## Configuration Files',
            '## Steam Workshop',
            '## Admin & RCON', 
            '## Saves, Backups & Wipes',
            '## Performance Tuning',
            '## Troubleshooting',
            '## Appendices'
        ]
        
        self.errors = []
        self.warnings = []
        
    def check_forbidden_terms(self, content, file_name):
        """Check for forbidden provider mentions"""
        content_lower = content.lower()
        found_terms = []
        
        for term in self.forbidden_terms:
            if term.lower() in content_lower:
                # Count occurrences for severity assessment
                count = content_lower.count(term.lower())
                found_terms.append(f"{term} ({count}x)")
                
        if found_terms:
            self.errors.append(f"{file_name}: Contains forbidden terms: {', '.join(found_terms)}")
            
    def check_html_entities(self, content, file_name):
        """Check for HTML entity escapes"""
        html_entities = ['&lt;', '&gt;', '&amp;', '&quot;', '&#']
        found_entities = []
        
        for entity in html_entities:
            if entity in content:
                count = content.count(entity)
                found_entities.append(f"{entity} ({count}x)")
                
        if found_entities:
            self.errors.append(f"{file_name}: Contains HTML entities: {', '.join(found_entities)}")
            
    def check_placeholder_handling(self, content, file_name):
        """Verify <PLACEHOLDER> text is literal, not escaped"""
        # Check for properly formatted placeholders
        placeholder_pattern = r'<[A-Z_]+>'
        placeholders = re.findall(placeholder_pattern, content)
        
        # Check for escaped placeholders that shouldn't be
        escaped_pattern = r'&lt;[A-Z_]+&gt;'
        escaped_placeholders = re.findall(escaped_pattern, content)
        
        if escaped_placeholders:
            self.errors.append(f"{file_name}: Contains escaped placeholders: {', '.join(escaped_placeholders)}")
            
        # Log found placeholders for information
        if placeholders:
            self.warnings.append(f"{file_name}: Contains {len(placeholders)} placeholders: {', '.join(placeholders[:3])}{'...' if len(placeholders) > 3 else ''}")
            
    def check_required_sections(self, content, file_name):
        """Verify all required sections are present"""
        missing_sections = []
        
        for section in self.required_sections:
            if section not in content:
                missing_sections.append(section)
                
        if missing_sections:
            self.errors.append(f"{file_name}: Missing required sections: {', '.join(missing_sections)}")
            
    def check_startup_parameters(self, content, file_name):
        """Verify sufficient startup parameters are documented"""
        # Look for startup parameters table
        param_pattern = r'\| [+\-][a-zA-Z0-9_.]+'
        parameters = re.findall(param_pattern, content)
        
        param_count = len(parameters)
        if param_count < 10:
            self.warnings.append(f"{file_name}: Only {param_count} startup parameters found (minimum 10 recommended)")
        elif param_count < 15:
            self.warnings.append(f"{file_name}: {param_count} startup parameters found (15+ recommended for exhaustive coverage)")
            
    def check_port_mappings(self, content, file_name):
        """Verify port mapping table is comprehensive"""
        # Look for port table entries
        port_pattern = r'\| [A-Za-z ]+ \| \d+ \| [A-Z]+ \|'
        port_entries = re.findall(port_pattern, content)
        
        port_count = len(port_entries)
        if port_count < 3:
            self.warnings.append(f"{file_name}: Only {port_count} port mappings found (3+ recommended)")
            
    def check_troubleshooting_depth(self, content, file_name):
        """Verify troubleshooting section is comprehensive"""
        # Look for troubleshooting subsections
        troubleshooting_match = re.search(r'## Troubleshooting.*?(?=## |\Z)', content, re.DOTALL)
        
        if troubleshooting_match:
            troubleshooting_content = troubleshooting_match.group(0)
            
            # Count subsections (### headers)
            subsection_pattern = r'###'
            subsections = re.findall(subsection_pattern, troubleshooting_content)
            subsection_count = len(subsections)
            
            if subsection_count < 5:
                self.warnings.append(f"{file_name}: Troubleshooting has only {subsection_count} subsections (5+ recommended for deep coverage)")
                
            # Check for common troubleshooting topics
            required_topics = ['startup', 'connection', 'performance', 'save', 'network']
            content_lower = troubleshooting_content.lower()
            missing_topics = [topic for topic in required_topics if topic not in content_lower]
            
            if missing_topics:
                self.warnings.append(f"{file_name}: Troubleshooting missing topics: {', '.join(missing_topics)}")
        else:
            self.errors.append(f"{file_name}: No troubleshooting section found")
            
    def validate_markdown_file(self, md_file):
        """Validate a single Markdown file"""
        try:
            with open(md_file, 'r', encoding='utf-8') as f:
                content = f.read()
                
            file_name = md_file.name
            
            # Run all validation checks
            self.check_forbidden_terms(content, file_name)
            self.check_html_entities(content, file_name)
            self.check_placeholder_handling(content, file_name)
            self.check_required_sections(content, file_name)
            self.check_startup_parameters(content, file_name)
            self.check_port_mappings(content, file_name)
            self.check_troubleshooting_depth(content, file_name)
            
        except Exception as e:
            self.errors.append(f"{md_file.name}: Error reading file: {e}")
            
    def validate_pdf_file(self, pdf_file):
        """Basic validation of PDF file"""
        try:
            size = pdf_file.stat().st_size
            
            # Check file size is reasonable (not empty, not too large)
            if size < 1000:  # Less than 1KB
                self.errors.append(f"{pdf_file.name}: PDF file too small ({size} bytes)")
            elif size > 50 * 1024 * 1024:  # More than 50MB
                self.warnings.append(f"{pdf_file.name}: PDF file very large ({size // 1024 // 1024} MB)")
            else:
                # File size is reasonable
                pass
                
        except Exception as e:
            self.errors.append(f"{pdf_file.name}: Error checking PDF: {e}")
            
    def validate_all_guides(self):
        """Validate all generated guides"""
        print("=== Quality Control Validation ===\n")
        
        if not self.md_dir.exists():
            self.errors.append(f"Markdown directory does not exist: {self.md_dir}")
            return False
            
        if not self.pdf_dir.exists():
            self.errors.append(f"PDF directory does not exist: {self.pdf_dir}")
            return False
            
        # Get all markdown files
        md_files = list(self.md_dir.glob("*.md"))
        pdf_files = list(self.pdf_dir.glob("*.pdf"))
        
        if not md_files:
            self.errors.append("No Markdown files found for validation")
            return False
            
        print(f"Validating {len(md_files)} Markdown files...")
        
        # Validate each markdown file
        for md_file in md_files:
            print(f"  Checking: {md_file.name}")
            self.validate_markdown_file(md_file)
            
            # Check if corresponding PDF exists
            pdf_name = md_file.stem + ".pdf"
            pdf_file = self.pdf_dir / pdf_name
            
            if pdf_file.exists():
                self.validate_pdf_file(pdf_file)
            else:
                self.errors.append(f"Missing PDF for {md_file.name}: {pdf_name}")
                
        print(f"\nValidation completed.")
        return self.print_results()
        
    def print_results(self):
        """Print validation results"""
        print("\n" + "=" * 50)
        print("VALIDATION RESULTS")
        print("=" * 50)
        
        total_issues = len(self.errors) + len(self.warnings)
        
        if self.errors:
            print(f"\n❌ ERRORS ({len(self.errors)}):")
            for error in self.errors:
                print(f"  • {error}")
                
        if self.warnings:
            print(f"\n⚠️  WARNINGS ({len(self.warnings)}):")
            for warning in self.warnings:
                print(f"  • {warning}")
                
        if not self.errors and not self.warnings:
            print("✅ All validations passed! No issues found.")
            
        print(f"\nSummary: {len(self.errors)} errors, {len(self.warnings)} warnings")
        
        # Return True if no errors (warnings are acceptable)
        return len(self.errors) == 0
        
    def generate_report(self):
        """Generate a JSON report of validation results"""
        report = {
            "timestamp": Path().cwd().name,
            "total_errors": len(self.errors),
            "total_warnings": len(self.warnings),
            "errors": self.errors,
            "warnings": self.warnings,
            "validation_passed": len(self.errors) == 0
        }
        
        report_file = self.pdf_dir / "quality_report.json"
        with open(report_file, 'w', encoding='utf-8') as f:
            json.dump(report, f, indent=2)
            
        print(f"Quality report saved: {report_file}")
        return report

if __name__ == "__main__":
    qc = GuideQualityControl()
    success = qc.validate_all_guides()
    qc.generate_report()
    
    sys.exit(0 if success else 1)