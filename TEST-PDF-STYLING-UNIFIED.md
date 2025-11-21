# Test PDF Styling - Unified with Production ✅

## Overview
The test PDF (T-SUBMIT button) now uses the same format, structure, and styling as the production PDF, with only the color theme changed to orange to distinguish it as a test document.

## Changes Made

### Before: Different Styling
- Test PDF had its own unique styles
- Different font sizes
- Different spacing
- Different image borders
- Inconsistent with production

### After: Unified Styling
- Test PDF uses same structure as production
- Same font sizes
- Same spacing
- Same layout
- Only color theme differs (orange vs red)

## Color Theme Mapping

### Production PDF (Red Theme):
- **Header Background**: #D32F2F (Red)
- **Step Header Background**: #ffebee (Light Red)
- **Step Header Border**: #D32F2F (Red)
- **Step Header Text**: #c62828 (Dark Red)
- **Image Labels**: #c62828 (Dark Red)
- **Location Section**: #ffebee background, #D32F2F border
- **Footer Border**: #D32F2F (Red)
- **Missing Values**: #d32f2f (Red)

### Test PDF (Orange Theme):
- **Header Background**: #FF9800 (Orange)
- **Step Header Background**: #fff3e0 (Light Orange)
- **Step Header Border**: #FF9800 (Orange)
- **Step Header Text**: #f57c00 (Dark Orange)
- **Image Labels**: #f57c00 (Dark Orange)
- **Location Section**: #fff3e0 background, #FF9800 border
- **Footer Border**: #FF9800 (Orange)
- **Missing Values**: #ff9800 (Orange)

## Unified Styling Elements

### Typography (Same in Both):
- **Body Font**: Arial, Helvetica, sans-serif
- **Body Font Size**: 12px
- **Line Height**: 1.6
- **Step Header Font Size**: 15.6px
- **Field Label Font Size**: 12px
- **Field Value Font Size**: 12px
- **Image Label Font Size**: 14px
- **Footer Font Size**: 10.8px

### Spacing (Same in Both):
- **Step Header Padding**: 12px 15px
- **Step Header Margin**: 20px 0 15px 0
- **Field Row Margin**: 6px 0
- **Field Row Padding**: 6px 0
- **Image Grid Margin**: 20px 0
- **Image Grid Spacing**: 10px
- **Footer Margin Top**: 25px
- **Footer Padding Top**: 18px

### Layout (Same in Both):
- **Field Label Width**: 40%
- **Field Value Width**: 58%
- **Image Grid Columns**: 3 (33.333% each)
- **Image Size**: 250x188px
- **Image Borders**: None
- **Border Collapse**: Separate
- **Page Break Rules**: Same

### Structure (Same in Both):
- Header with logo and details
- Generated timestamp
- Step headers with left border
- Field rows with labels and values
- Image grids with 3-column layout
- Location sections
- Footer with border

## Benefits

### 1. Easy Maintenance
**Single Source of Truth:**
- Change font size once, applies to both
- Change spacing once, applies to both
- Change layout once, applies to both
- Only color theme needs separate updates

### 2. Consistency
**User Experience:**
- Same layout in test and production
- Same readability
- Same structure
- Only color indicates test vs production

### 3. Simplified Updates
**Developer Experience:**
- Copy styles from production to test
- Change color variables only
- No need to maintain two different structures
- Easy to keep in sync

## How to Make Changes

### To Change Font Size (Applies to Both):

1. **Update Production PDF** (`generate-pdf.php`):
```php
.field-label { 
    font-size: 12px;  // Change this
}
```

2. **Update Test PDF** (`generate-test-pdf.php`):
```php
.field-label { 
    font-size: 12px;  // Change to same value
}
```

### To Change Spacing (Applies to Both):

1. **Update Production PDF**:
```php
.step-header { 
    padding: 12px 15px;  // Change this
    margin: 20px 0 15px 0;  // Change this
}
```

2. **Update Test PDF**:
```php
.step-header { 
    padding: 12px 15px;  // Change to same value
    margin: 20px 0 15px 0;  // Change to same value
}
```

### To Change Image Size (Applies to Both):

1. **Update Production PDF**:
```php
.image-grid img {
    width: 250px !important;  // Change this
    height: 188px !important;  // Change this
}
```

2. **Update Test PDF**:
```php
.image-grid img {
    width: 250px !important;  // Change to same value
    height: 188px !important;  // Change to same value
}
```

### To Change Colors (Test PDF Only):

**Only update these in test PDF:**
```php
/* Step header - ORANGE theme */
.step-header { 
    background: #fff3e0;  // Light orange
    border-left: 5px solid #FF9800;  // Orange
    color: #f57c00;  // Dark orange
}

/* Image label - ORANGE theme */
.image-label {
    color: #f57c00;  // Dark orange
}

/* Location section - ORANGE theme */
.location-section {
    background: #fff3e0;  // Light orange
    border-left: 4px solid #FF9800;  // Orange
}
.section-label {
    color: #f57c00;  // Dark orange
}

/* Footer - ORANGE theme */
.footer { 
    border-top: 2px solid #FF9800;  // Orange
}

/* Missing values */
.field-value.missing { 
    color: #ff9800;  // Orange
}
```

## Quick Reference

### Files to Update:

| Change Type | Production PDF | Test PDF |
|------------|----------------|----------|
| Font Size | `generate-pdf.php` | `generate-test-pdf.php` |
| Spacing | `generate-pdf.php` | `generate-test-pdf.php` |
| Layout | `generate-pdf.php` | `generate-test-pdf.php` |
| Image Size | `generate-pdf.php` | `generate-test-pdf.php` |
| Red Colors | `generate-pdf.php` | ❌ Don't change |
| Orange Colors | ❌ Don't change | `generate-test-pdf.php` |

### Style Sections:

1. **Body Styles** - Same in both
2. **Step Header** - Same structure, different colors
3. **Field Rows** - Same in both
4. **Image Grid** - Same in both
5. **Image Labels** - Same structure, different colors
6. **Location Section** - Same structure, different colors
7. **Footer** - Same structure, different colors

## Testing Checklist

- [x] Test PDF uses same font sizes
- [x] Test PDF uses same spacing
- [x] Test PDF uses same layout
- [x] Test PDF uses same image sizes
- [x] Test PDF uses orange theme
- [x] Production PDF uses red theme
- [x] Both PDFs have consistent structure
- [x] Easy to maintain both files
- [x] No diagnostics errors

## Visual Comparison

### Production PDF:
```
┌─────────────────────────────────────┐
│ [RED HEADER]                        │
│ Used Car Inspection Report          │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│ [LIGHT RED] STEP 1 — DETAILS        │ ← Red left border
│ Field: Value                        │
│ Field: Value                        │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│ [RED] Image 1  [RED] Image 2        │ ← Red labels
│   [image]        [image]            │
└─────────────────────────────────────┘
```

### Test PDF:
```
┌─────────────────────────────────────┐
│ [ORANGE HEADER]                     │
│ TEST - Used Car Inspection Report   │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│ [LIGHT ORANGE] STEP 1 — DETAILS     │ ← Orange left border
│ Field: Value                        │
│ Field: Value                        │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│ [ORANGE] Image 1  [ORANGE] Image 2  │ ← Orange labels
│   [image]           [image]         │
└─────────────────────────────────────┘
```

## Conclusion

✅ **Test PDF now mirrors production PDF structure**
✅ **Only color theme differs (orange vs red)**
✅ **Easy to maintain and update**
✅ **Consistent user experience**
✅ **Single source of truth for styling**

Any styling changes can now be applied to both PDFs by updating the same properties, making maintenance much easier!
