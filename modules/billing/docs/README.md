# Documentation System

## Overview

The billing module now includes a flexible documentation browser that organizes documentation into categories with an easy-to-navigate interface.

## Structure

Documentation is organized in the `/modules/billing/docs/` folder with the following structure:

```
docs/
├── category-name-1/
│   ├── index.php          (Required: Documentation content)
│   ├── metadata.json      (Required: Category and ordering info)
│   └── icon.png or icon.jpg (Required: Category icon)
├── category-name-2/
│   ├── index.php
│   ├── metadata.json
│   └── icon.png
└── ...
```

## Creating New Documentation

### 1. Create a Folder

Create a new folder in `/modules/billing/docs/` with a descriptive name (lowercase, hyphens for spaces):

```bash
mkdir /modules/billing/docs/my-new-doc
```

### 2. Create metadata.json

This file defines how the documentation appears in the list:

```json
{
    "name": "My Documentation Title",
    "description": "A brief description of this documentation",
    "category": "game",
    "order": 10
}
```

**Fields:**
- `name`: Display name shown in the documentation list
- `description`: Brief description shown on the card
- `category`: One of: `game`, `panel`, `mods`, `troubleshooting`, `other`
- `order`: Sort order within the category (lower numbers appear first)

### 3. Create index.php

This file contains the actual documentation content. Use PHP and HTML:

```php
<?php
/**
 * My Documentation
 */
?>
<h1>My Documentation Title</h1>

<h2>Section 1</h2>
<p>Your content here...</p>

<h3>Subsection</h3>
<ul>
    <li>Item 1</li>
    <li>Item 2</li>
</ul>

<h2>Code Examples</h2>
<pre><code>
# Your code here
command --option value
</code></pre>
```

The documentation system automatically styles:
- Headings (h1-h4)
- Links (styled with accent color)
- Code blocks (with dark background)
- Lists and other HTML elements

### 4. Add an Icon

Add either `icon.png` or `icon.jpg` to the folder. Recommended size: 60x60 pixels or larger (will be scaled down).

If no icon is provided, a default document emoji (📄) will be shown.

## Categories

Documentation is organized into these categories:

- **game** - Game-specific server guides
- **panel** - Panel usage and features
- **mods** - Mods and addon documentation
- **troubleshooting** - Problem-solving guides
- **other** - Miscellaneous documentation

Categories are sorted and labeled automatically on the documentation page.

## Example Documentation

See the included examples:

1. **minecraft** - Game server documentation example
2. **getting-started** - Panel documentation example
3. **common-issues** - Troubleshooting documentation example

## Accessing Documentation

Users can access documentation at:
- `/modules/billing/docs.php` - Main documentation list
- `/modules/billing/docs.php?action=view&doc=folder-name` - Specific doc

A "Documentation" link is added to the main navigation menu.

## Best Practices

1. **Keep it Organized**: Use clear, descriptive folder names
2. **Consistent Naming**: Use lowercase and hyphens (e.g., `my-game-guide`)
3. **Good Descriptions**: Write helpful metadata descriptions
4. **Visual Icons**: Use recognizable icons for each category
5. **Test Content**: Preview documentation after creating it
6. **Regular Updates**: Keep documentation current with panel changes

## Migration from Old System

The old docs folder with game markdown files has been moved to `/modules/billing/docs_old/` for reference. The new system provides:

- Better organization by category
- Consistent styling
- Easier navigation
- Extensible structure for any type of documentation

To migrate old documentation:
1. Create a new folder for each document
2. Convert markdown to HTML in index.php
3. Add appropriate metadata.json
4. Add an icon image

## Troubleshooting

### Documentation not appearing
- Check that folder has all three required files (index.php, metadata.json, icon)
- Verify metadata.json is valid JSON
- Ensure file permissions allow reading

### Styling issues
- The system uses inline styles from docs.php
- Custom styles in index.php may conflict
- Keep content semantic (use proper HTML tags)

### Icons not showing
- Check file exists and is named exactly `icon.png` or `icon.jpg`
- Verify image file is not corrupted
- Try a smaller image size if very large
