# Dashboard Color Theme Guide

## Quick Start

All theme colors are defined in `styles.css` using CSS variables. Change colors in ONE place and it affects ALL pages!

## How to Change Main Colors

### Option 1: Change Primary Color (Most Common)

Open `styles.css` and modify these variables at the top:

```css
:root {
    /* Primary Brand Colors */
    --primary-color: #06B6D4;  /* Main brand color */
    --primary-dark: #0891B2;   /* Darker shade for gradients/hover */
    --primary-light: #22D3EE;  /* Lighter shade */
}
```

### Popular Color Themes:

#### 🟣 Purple Theme
```css
--primary-color: #8B5CF6;
--primary-dark: #7C3AED;
--primary-light: #A78BFA;
```

#### 🟢 Green Theme
```css
--primary-color: #10B981;
--primary-dark: #059669;
--primary-light: #34D399;
```

#### 🔵 Blue Theme
```css
--primary-color: #3B82F6;
--primary-dark: #2563EB;
--primary-light: #60A5FA;
```

#### 🟠 Orange Theme
```css
--primary-color: #F59E0B;
--primary-dark: #D97706;
--primary-light: #FBBF24;
```

#### 🔴 Red Theme
```css
--primary-color: #EF4444;
--primary-dark: #DC2626;
--primary-light: #F87171;
```

#### ⚫ Dark Theme
```css
--primary-color: #1F2937;
--primary-dark: #111827;
--primary-light: #374151;
```

## Using Theme Classes in HTML

### Buttons
```html
<button class="btn-primary">Primary Button</button>
<button class="btn-secondary">Secondary Button</button>
<button class="btn-success">Success Button</button>
<button class="btn-warning">Warning Button</button>
<button class="btn-danger">Danger Button</button>
```

### Badges
```html
<span class="badge-primary">Primary</span>
<span class="badge-success">Success</span>
<span class="badge-warning">Warning</span>
<span class="badge-danger">Danger</span>
<span class="badge-info">Info</span>
```

### Cards
```html
<div class="card">Basic Card</div>
<div class="card card-hover">Card with Hover Effect</div>
```

### Background Colors
```html
<div class="primary-bg">Primary Background</div>
<div class="primary-bg-light">Light Primary Background</div>
```

### Text Colors
```html
<span class="primary-text">Primary Colored Text</span>
```

## Advanced Customization

### Custom Gradients
```css
--gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
```

### Custom Shadows
```css
--shadow-primary: 0 10px 20px rgba(6, 182, 212, 0.25);
```

### Border Radius
```css
--radius-sm: 0.375rem;   /* Small */
--radius-md: 0.5rem;     /* Medium */
--radius-lg: 0.75rem;    /* Large */
--radius-xl: 1rem;       /* Extra Large */
--radius-2xl: 1.5rem;    /* 2X Large */
```

### Spacing
```css
--spacing-xs: 0.25rem;   /* Extra Small */
--spacing-sm: 0.5rem;    /* Small */
--spacing-md: 1rem;      /* Medium */
--spacing-lg: 1.5rem;    /* Large */
--spacing-xl: 2rem;      /* Extra Large */
```

## Multi-Page Setup

1. **Save `styles.css`** in your project root or assets folder
2. **Link it in all HTML pages:**
   ```html
   <link rel="stylesheet" href="styles.css">
   ```
3. **Change colors once** in `styles.css`
4. **All pages update automatically!** ✨

## File Structure
```
Dashboard/
├── dashboard.html
├── styles.css          ← Main color theme file
├── page2.html          ← Links to styles.css
├── page3.html          ← Links to styles.css
└── ...
```

## Tips

1. **Test Changes**: Use browser DevTools to test colors before committing
2. **Contrast**: Ensure text has enough contrast against backgrounds
3. **Consistency**: Use the predefined classes instead of inline styles
4. **Backup**: Keep a backup of your original `styles.css`

## Color Palette Generator Tools

- [Coolors.co](https://coolors.co/) - Generate color palettes
- [Adobe Color](https://color.adobe.com/) - Create color schemes
- [Tailwind Colors](https://tailwindcss.com/docs/customizing-colors) - Pre-built color palettes

## Support

For questions or issues, refer to the CSS variables in `styles.css` or check the comments for usage examples.

---
**Made with ❤️ for easy theme customization**
