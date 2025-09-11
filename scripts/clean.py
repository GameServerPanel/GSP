#!/usr/bin/env python3
"""
Clean Output Directories

Removes all generated files from out/md and out/pdfs directories before a full rebuild.
"""

import os
import shutil
from pathlib import Path

def clean_output_directories():
    """Remove all files from output directories"""
    base_dir = Path(__file__).parent.parent
    
    # Define directories to clean
    directories_to_clean = [
        base_dir / "out" / "md",
        base_dir / "out" / "pdfs"
    ]
    
    for directory in directories_to_clean:
        if directory.exists():
            print(f"Cleaning {directory}...")
            # Remove all files and subdirectories
            for item in directory.iterdir():
                if item.is_file():
                    item.unlink()
                    print(f"  Removed file: {item.name}")
                elif item.is_dir():
                    shutil.rmtree(item)
                    print(f"  Removed directory: {item.name}")
        else:
            print(f"Directory does not exist: {directory}")
    
    print("Clean completed.")

if __name__ == "__main__":
    clean_output_directories()