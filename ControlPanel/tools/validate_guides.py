#!/usr/bin/env python3
"""
Server Admin Guide Validator

Validates generated server admin guides against quality gates and requirements.
Ensures guides meet the "exhaustive" standard specified in the requirements.
"""

import os
import sys
import yaml
import json
from pathlib import Path
import re

class GuideValidator:
    def __init__(self, docs_dir="docs/games", pdfs_dir="dist/pdfs", data_dir="data/games"):
        self.docs_dir = Path(docs_dir)
        self.pdfs_dir = Path(pdfs_dir)
        self.data_dir = Path(data_dir)
        self.errors = []
        self.warnings = []
        
    def validate_all(self):
        """Run all validation checks"""
        print("=== Server Admin Guide Validation ===\n")
        
        self.validate_directory_structure()
        self.validate_yaml_data()
        self.validate_markdown_guides()
        self.validate_pdf_files()
        self.validate_manifest()
        self.validate_index_page()
        
        return self.print_results()
        
    def validate_directory_structure(self):
        """Validate required directories exist"""
        print("Checking directory structure...")
        
        required_dirs = [self.docs_dir, self.pdfs_dir, self.data_dir]
        for directory in required_dirs:
            if not directory.exists():
                self.errors.append(f"Required directory missing: {directory}")
                
        required_files = [
            self.docs_dir / "_index.md",
            self.pdfs_dir / "manifest.json"
        ]
        
        for file_path in required_files:
            if not file_path.exists():
                self.errors.append(f"Required file missing: {file_path}")
                
        print("✓ Directory structure validated\n")
        
    def validate_yaml_data(self):
        """Validate YAML game data meets exhaustive requirements"""
        print("Checking YAML game data...")
        
        yaml_files = list(self.data_dir.glob("*.yml")) + list(self.data_dir.glob("*.yaml"))
        
        for yaml_file in yaml_files:
            try:
                with open(yaml_file, 'r', encoding='utf-8') as f:
                    game_data = yaml.safe_load(f)
                    self.validate_single_game_yaml(game_data, yaml_file)
            except Exception as e:
                self.errors.append(f"Error reading {yaml_file}: {e}")
                
        print("✓ YAML data validated\n")
        
    def validate_single_game_yaml(self, game_data, filename):
        """Validate individual game YAML meets requirements"""
        game_name = game_data.get('name', 'Unknown')
        
        # Check required sections
        required_sections = ['name', 'supports_workshop', 'startup', 'configs', 'troubleshooting']
        for section in required_sections:
            if section not in game_data:
                self.errors.append(f"{filename}: Missing required section '{section}'")
                
        # Validate startup parameters (minimum 10 flags)
        flags = game_data.get('startup', {}).get('flags', [])
        if len(flags) < 10:
            self.warnings.append(f"{game_name}: Only {len(flags)} startup flags (minimum 10 recommended)")
            
        # Validate config files (minimum 8 entries)
        configs = game_data.get('configs', [])
        if len(configs) < 8:
            self.warnings.append(f"{game_name}: Only {len(configs)} config entries (minimum 8 recommended)")
            
        # Validate port mapping
        ports = game_data.get('startup', {}).get('ports', [])
        if not ports:
            self.errors.append(f"{game_name}: No port mapping defined")
        else:
            for port in ports:
                required_port_fields = ['label', 'port', 'proto', 'relative']
                for field in required_port_fields:
                    if field not in port:
                        self.errors.append(f"{game_name}: Port entry missing '{field}' field")
                        
    def validate_markdown_guides(self):
        """Validate generated Markdown guides"""
        print("Checking Markdown guides...")
        
        required_sections = [
            "Quick Start", 
            "Full Port Map", 
            "Startup Parameters \\(EXHAUSTIVE\\)",
            "Configuration Files & Paths \\(ALL\\)",
            "Steam Workshop",
            "Player & Server Management",
            "Troubleshooting \\(Deep\\)",
            "Appendices"
        ]
        
        game_dirs = [d for d in self.docs_dir.iterdir() if d.is_dir()]
        
        for game_dir in game_dirs:
            md_file = game_dir / "index.md"
            if not md_file.exists():
                self.errors.append(f"Missing Markdown file: {md_file}")
                continue
                
            with open(md_file, 'r', encoding='utf-8') as f:
                content = f.read()
                
            # Check for all required sections
            for section in required_sections:
                pattern = f"## {section}"
                if not re.search(pattern, content):
                    self.errors.append(f"{md_file}: Missing required section '{section}'")
                    
            # Check for startup parameters table
            if "| Flag/Param | Default | Type/Range | Description | Example |" not in content:
                self.errors.append(f"{md_file}: Missing startup parameters table")
                
            # Check for port mapping table
            if "| Feature | Port | Protocol | Relation | Notes |" not in content:
                self.errors.append(f"{md_file}: Missing port mapping table")
                
            # Check for no TBD or placeholder content
            placeholders = ["TBD", "TODO", "coming soon", "placeholder"]
            for placeholder in placeholders:
                if placeholder.lower() in content.lower():
                    self.warnings.append(f"{md_file}: Contains placeholder text '{placeholder}'")
                    
        print("✓ Markdown guides validated\n")
        
    def validate_pdf_files(self):
        """Validate PDF files exist and have reasonable size"""
        print("Checking PDF files...")
        
        game_dirs = [d for d in self.docs_dir.iterdir() if d.is_dir()]
        
        for game_dir in game_dirs:
            slug = game_dir.name
            pdf_file = self.pdfs_dir / f"{slug}__Server_Admin_Guide_v1.pdf"
            
            if not pdf_file.exists():
                self.errors.append(f"Missing PDF file: {pdf_file}")
                continue
                
            # Check file size (should be at least 20KB for a comprehensive guide)
            file_size = pdf_file.stat().st_size
            if file_size < 20480:  # 20KB
                self.warnings.append(f"{pdf_file}: Small file size ({file_size} bytes) - may indicate incomplete content")
                
        print("✓ PDF files validated\n")
        
    def validate_manifest(self):
        """Validate manifest.json structure and content"""
        print("Checking manifest...")
        
        manifest_file = self.pdfs_dir / "manifest.json"
        if not manifest_file.exists():
            self.errors.append("Missing manifest.json file")
            return
            
        try:
            with open(manifest_file, 'r', encoding='utf-8') as f:
                manifest = json.load(f)
                
            # Check required fields
            required_fields = ["generated", "total_games", "games"]
            for field in required_fields:
                if field not in manifest:
                    self.errors.append(f"Manifest missing required field: {field}")
                    
            # Validate games entries
            if "games" in manifest:
                for game in manifest["games"]:
                    required_game_fields = ["title", "slug", "appid", "engine", "workshop_support", "ports", "config_files", "last_updated", "markdown_path", "pdf_path"]
                    for field in required_game_fields:
                        if field not in game:
                            self.errors.append(f"Manifest game entry missing field: {field}")
                            
        except json.JSONDecodeError as e:
            self.errors.append(f"Invalid JSON in manifest: {e}")
            
        print("✓ Manifest validated\n")
        
    def validate_index_page(self):
        """Validate index page content"""
        print("Checking index page...")
        
        index_file = self.docs_dir / "_index.md"
        if not index_file.exists():
            self.errors.append("Missing index page")
            return
            
        with open(index_file, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # Check for required sections
        required_content = [
            "# Game Server Admin Guides",
            "## Available Guides",
            "| Game | Engine | Workshop | AppID | Documentation | PDF Guide |",
            "## Statistics"
        ]
        
        for required in required_content:
            if required not in content:
                self.errors.append(f"Index page missing required content: {required}")
                
        print("✓ Index page validated\n")
        
    def print_results(self):
        """Print validation results"""
        print("=== Validation Results ===\n")
        
        if self.errors:
            print(f"❌ ERRORS ({len(self.errors)}):")
            for error in self.errors:
                print(f"  • {error}")
            print()
            
        if self.warnings:
            print(f"⚠️  WARNINGS ({len(self.warnings)}):")
            for warning in self.warnings:
                print(f"  • {warning}")
            print()
            
        if not self.errors and not self.warnings:
            print("✅ All validation checks passed!")
        elif not self.errors:
            print("✅ No critical errors found (warnings can be addressed)")
        else:
            print(f"❌ Validation failed with {len(self.errors)} errors")
            
        return len(self.errors) == 0

if __name__ == "__main__":
    validator = GuideValidator()
    success = validator.validate_all()
    sys.exit(0 if success else 1)