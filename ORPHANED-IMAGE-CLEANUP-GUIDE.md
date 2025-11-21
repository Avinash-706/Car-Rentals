# Orphaned Image Cleanup - Complete Guide

## Problem Solved
**Issue #7:** When users replace an image (upload a new one for the same field), the old image file was never deleted from the server, causing disk space to fill up with unused images.

## Solution Implemented

### 1. ✅ Automatic Cleanup on Image Replace
**File:** `upload-image.php`

When a user uploads a new image for a field that already has an image:
1. System checks if the field already has an image in the draft
2. If yes, deletes the old image file
3. Also deletes the old thumbnail
4. Logs the deletion in the audit trail
5. Saves the new image

**Code Added:**
```php
// CLEANUP: Delete old image if this field already has one (user is replacing)
if (isset($draftData['uploaded_files'][$fieldName])) {
    $oldFilePath = $draftData['uploaded_files'][$fieldName];
    // ... delete old file and thumbnail
}
```

### 2. ✅ Enhanced Draft Discard Cleanup
**File:** `drafts/discard.php`

When a draft is discarded:
1. Loads the draft JSON
2. Finds all uploaded images
3. Deletes each image file
4. Deletes all thumbnails
5. Handles both absolute and relative paths
6. Logs all deletions

**Improvements:**
- Now handles both absolute and relative paths
- Better error logging
- Returns count of deleted images

### 3. ✅ Orphaned Image Cleanup Utility
**File:** `cleanup-orphaned-images.php`

A utility script to find and delete images that are not referenced in any draft:
- Scans all images in `uploads/drafts/`
- Checks all draft JSON files for references
- Identifies orphaned images
- Can run in dry-run mode (preview only)
- Deletes orphaned images and thumbnails
- Reports space freed

## Usage

### Automatic Cleanup (Built-in)

**When Replacing Images:**
```
1. User uploads image for "carPhoto"
2. User uploads different image for "carPhoto" (replace)
3. ✅ Old image automatically deleted
4. ✅ Old thumbnail automatically deleted
5. ✅ Logged in audit trail
```

**When Discarding Draft:**
```
1. User clicks "Discard Draft"
2. ✅ All draft images deleted
3. ✅ All thumbnails deleted
4. ✅ Draft JSON deleted
5. ✅ Audit log deleted
```

### Manual Cleanup (Utility Script)

**Dry Run (Preview Only):**
```
GET /cleanup-orphaned-images.php?dry_run=true
```

**Response:**
```json
{
  "success": true,
  "message": "Dry run completed. No files were deleted.",
  "dry_run": true,
  "stats": {
    "total_images": 150,
    "referenced_images": 120,
    "orphaned_images": 30,
    "deleted_images": 0,
    "space_freed": 0
  },
  "orphaned_files": [
    {
      "name": "1732123456_guest_abc123_car.jpg",
      "size": 2048576,
      "size_formatted": "2.00 MB",
      "path": "C:/xampp/htdocs/project/uploads/drafts/...",
      "modified": "2025-11-20 15:30:45"
    }
  ]
}
```

**Actual Cleanup:**
```
GET /cleanup-orphaned-images.php
```

**Response:**
```json
{
  "success": true,
  "message": "Cleanup completed successfully.",
  "dry_run": false,
  "stats": {
    "total_images": 150,
    "referenced_images": 120,
    "orphaned_images": 30,
    "deleted_images": 30,
    "failed_deletions": 0,
    "space_freed": 61440000,
    "space_freed_formatted": "58.59 MB"
  }
}
```

## Testing

### Test 1: Image Replacement
```
1. Open index.php
2. Upload image for "Car Photo"
3. Note the filename in browser console
4. Upload different image for "Car Photo"
5. ✅ Check uploads/drafts/ folder - old image should be gone
6. ✅ Check console - should log "Deleted replaced image"
```

### Test 2: Draft Discard
```
1. Upload 5 images
2. Click "Save Draft"
3. Note the draft ID
4. Check uploads/drafts/ folder - images should exist
5. Click "Discard Draft"
6. ✅ Check uploads/drafts/ folder - all images should be gone
7. ✅ Draft JSON should be deleted
```

### Test 3: Orphaned Image Cleanup
```
1. Manually copy some test images to uploads/drafts/
2. Run: cleanup-orphaned-images.php?dry_run=true
3. ✅ Should list the test images as orphaned
4. Run: cleanup-orphaned-images.php
5. ✅ Test images should be deleted
6. ✅ Response should show space freed
```

## Scheduled Cleanup (Recommended)

### Option 1: Cron Job (Linux)
```bash
# Run cleanup daily at 2 AM
0 2 * * * /usr/bin/php /path/to/project/cleanup-orphaned-images.php
```

### Option 2: Windows Task Scheduler
```
Program: C:\xampp\php\php.exe
Arguments: C:\xampp\htdocs\project\cleanup-orphaned-images.php
Trigger: Daily at 2:00 AM
```

### Option 3: Manual (Admin Panel)
Create an admin page that calls the cleanup script:
```php
<button onclick="cleanupOrphanedImages()">Cleanup Orphaned Images</button>

<script>
function cleanupOrphanedImages() {
    if (confirm('Run cleanup? This will delete orphaned images.')) {
        fetch('cleanup-orphaned-images.php')
            .then(response => response.json())
            .then(data => {
                alert(`Cleanup complete!\n` +
                      `Deleted: ${data.stats.deleted_images} images\n` +
                      `Space freed: ${data.stats.space_freed_formatted}`);
            });
    }
}
</script>
```

## Audit Trail

All image operations are logged in `drafts/audit/{draft_id}.log`:

```
2025-11-21 22:50:45 - Image uploaded: carPhoto -> uploads/drafts/1732123456_guest_abc123_car.jpg
2025-11-21 22:51:30 - Image uploaded: carPhoto -> uploads/drafts/1732123490_guest_def456_car.jpg (replaced old image)
2025-11-21 22:52:15 - Image uploaded: enginePhoto -> uploads/drafts/1732123535_guest_ghi789_engine.jpg
```

## Benefits

### 1. **Disk Space Savings**
- No accumulation of replaced images
- Automatic cleanup on discard
- Manual cleanup utility for maintenance

### 2. **Better Performance**
- Fewer files in drafts directory
- Faster directory scans
- Reduced backup sizes

### 3. **Security**
- Old images can't be accessed after replacement
- Complete cleanup on draft discard
- No orphaned sensitive data

### 4. **Maintenance**
- Easy to identify orphaned images
- Dry-run mode for safe testing
- Detailed statistics and logging

## File Changes Summary

| File | Changes | Status |
|------|---------|--------|
| `upload-image.php` | Added automatic old image deletion on replace | ✅ Complete |
| `drafts/discard.php` | Enhanced to handle both path types | ✅ Complete |
| `cleanup-orphaned-images.php` | New utility script | ✅ Created |
| `ORPHANED-IMAGE-CLEANUP-GUIDE.md` | Documentation | ✅ Created |

## API Response Changes

### upload-image.php Response
**New field added:**
```json
{
  "success": true,
  "message": "Image uploaded and compressed successfully",
  "file_path": "uploads/drafts/xxxxx.jpg",
  "old_image_deleted": true  // ← NEW FIELD
}
```

### drafts/discard.php Response
**Enhanced fields:**
```json
{
  "success": true,
  "message": "Draft discarded successfully",
  "deleted_images": 8,  // ← Now accurate
  "deleted_files": 10
}
```

## Monitoring

### Check Disk Usage
```bash
# Linux
du -sh uploads/drafts/

# Windows PowerShell
Get-ChildItem uploads/drafts -Recurse | Measure-Object -Property Length -Sum
```

### Check Orphaned Images
```
GET /cleanup-orphaned-images.php?dry_run=true
```

### Check Audit Logs
```
GET /drafts/audit/{draft_id}.log
```

## Troubleshooting

### Issue: Old images not being deleted
**Check:**
1. File permissions on uploads/drafts/ folder
2. PHP error log for "Failed to delete old image" messages
3. Verify DirectoryManager is working correctly

### Issue: Cleanup script finds no orphaned images
**Possible causes:**
1. All images are properly referenced (good!)
2. Path format mismatch (check absolute vs relative)
3. Draft JSON files are missing

### Issue: "Permission denied" errors
**Solution:**
```bash
# Linux
chmod 755 uploads/drafts/
chmod 644 uploads/drafts/*

# Windows
Right-click folder → Properties → Security → Edit permissions
```

## Status: ✅ COMPLETE

Orphaned image cleanup is now fully implemented with:
- ✅ Automatic cleanup on image replace
- ✅ Complete cleanup on draft discard
- ✅ Manual cleanup utility
- ✅ Dry-run mode for testing
- ✅ Detailed logging and statistics
- ✅ Both absolute and relative path support

---

**Last Updated:** November 21, 2025
**Issue:** #7 - No Cleanup of Orphaned Images
**Status:** Resolved ✅
