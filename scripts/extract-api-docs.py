#!/usr/bin/env python3
"""
Extract Persian API documentation from Kavenegar official source
and convert to Markdown format.
"""

import requests
from bs4 import BeautifulSoup
import html2text
import sys
import hashlib
import json
from datetime import datetime

def fetch_and_convert():
    """Fetch HTML from Kavenegar and convert to Markdown"""
    
    print("Fetching content from kavenegar.com/rest.html...")
    response = requests.get('https://kavenegar.com/rest.html')
    response.encoding = 'utf-8'
    
    if response.status_code != 200:
        print(f"Error: Failed to fetch content (status {response.status_code})")
        sys.exit(1)
    
    print("Parsing HTML content...")
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # Find the main documentation content
    docs_content = soup.find('div', class_='docs-content')
    
    if not docs_content:
        print("Error: Could not find documentation content")
        sys.exit(1)
    
    print("Converting to Markdown...")
    h = html2text.HTML2Text()
    h.body_width = 0  # Don't wrap lines
    h.unicode_snob = True  # Use unicode
    h.ignore_links = False
    h.ignore_images = False
    h.ignore_emphasis = False
    
    markdown_content = h.handle(str(docs_content))
    
    # Add header
    final_content = "# مستندات REST API کاوه نگار\n\n"
    final_content += "> این مستند کپی دقیق از [مستندات رسمی کاوه نگار](https://kavenegar.com/rest.html) به فرمت Markdown می‌باشد.\n\n"
    final_content += markdown_content
    
    # Write to file
    output_file = 'docs/api-endpoints.md'
    print(f"Writing to {output_file}...")
    
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(final_content)
    
    # Generate manifest
    content_hash = hashlib.sha256(final_content.encode('utf-8')).hexdigest()
    manifest = {
        'generated_at': datetime.now().isoformat(),
        'source_url': 'https://kavenegar.com/rest.html',
        'content_hash': content_hash,
        'file_size': len(final_content),
        'character_count': len(final_content),
        'format': 'markdown',
        'encoding': 'utf-8'
    }
    
    manifest_file = 'docs/api-endpoints.manifest.json'
    print(f"Writing manifest to {manifest_file}...")
    
    with open(manifest_file, 'w', encoding='utf-8') as f:
        json.dump(manifest, f, indent=2, ensure_ascii=False)
    
    print("✓ Successfully created Persian API documentation!")
    print(f"  File size: {len(final_content)} characters")
    print(f"  Content hash: {content_hash[:16]}...")
    print(f"  Manifest: {manifest_file}")

if __name__ == '__main__':
    try:
        fetch_and_convert()
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)
