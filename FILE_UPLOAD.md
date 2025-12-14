# File Upload Support in Cypress Module

## Overview
The Cypress module now fully supports capturing and generating test code for file upload interactions using Cypress's modern `cy.selectFile()` command (Cypress 9.3.0+).

## Features

### ✅ Automatic File Upload Detection
- Detects file input changes automatically during event capture
- Extracts file metadata (name, size, type, last modified)
- Supports both single and multiple file uploads
- Captures drag-and-drop file uploads
- Records accept attribute and multiple attribute

### ✅ Comprehensive File Metadata
For each uploaded file, the system captures:
```javascript
{
  name: "example.pdf",        // File name
  size: 1024000,              // Size in bytes
  type: "application/pdf",    // MIME type
  lastModified: 1701234567890 // Timestamp
}
```

### ✅ Smart Code Generation
Generates proper Cypress commands:

**Single File:**
```javascript
// Upload file: example.pdf (application/pdf, 1000.00KB) // Profile Picture
cy.get('input[type="file"]').selectFile('cypress/fixtures/example.pdf')
```

**Multiple Files:**
```javascript
// Upload 3 files: file1.pdf, file2.jpg, file3.png // Documents
cy.get('input[type="file"]').selectFile([
  'cypress/fixtures/file1.pdf',
  'cypress/fixtures/file2.jpg',
  'cypress/fixtures/file3.png'
])
```

## How It Works

### 1. Event Capture
When a user selects files via `<input type="file">`, the system:
1. Detects the `change` event on the file input
2. Extracts metadata for all selected files
3. Records the event with file information
4. Stores event data for code generation

### 2. Code Generation
When generating Cypress code:
1. Identifies file upload events in the captured sequence
2. Creates proper `cy.selectFile()` commands
3. Maps file names to `cypress/fixtures/` directory
4. Handles single and multiple file scenarios
5. Adds descriptive comments with file details

## Usage Guide

### Step 1: Capture File Upload
1. Start event capture in your test case
2. Navigate to page with file input
3. Select one or more files
4. The file upload is automatically captured

### Step 2: Generate Test Code
1. Save captured events
2. Click "Generate Code"
3. Review the generated `cy.selectFile()` commands

### Step 3: Setup Fixtures
Place your test files in the Cypress fixtures directory:
```
cypress/
  fixtures/
    example.pdf
    test-image.jpg
    sample-data.csv
```

### Step 4: Run Tests
Execute your generated test:
```bash
npx cypress run --spec "cypress/e2e/your-test.cy.js"
```

## Advanced Usage

### Drag and Drop Upload
To test drag-and-drop file uploads:
```javascript
cy.get('.dropzone').selectFile('cypress/fixtures/file.pdf', {
  action: 'drag-drop'
})
```

### Force Hidden Input
For hidden file inputs:
```javascript
cy.get('input[type="file"]').selectFile('cypress/fixtures/file.pdf', {
  force: true
})
```

### Create Files Programmatically
```javascript
cy.writeFile('cypress/fixtures/generated.txt', 'Test content')
cy.get('input[type="file"]').selectFile('cypress/fixtures/generated.txt')
```

### Upload as Blob/Buffer
```javascript
cy.fixture('example.json').then(fileContent => {
  cy.get('input[type="file"]').selectFile({
    contents: Cypress.Buffer.from(JSON.stringify(fileContent)),
    fileName: 'data.json',
    mimeType: 'application/json'
  })
})
```

## Event Data Structure

### Captured File Upload Event
```javascript
{
  type: "file_upload",
  element: "INPUT",
  input_type: "file",
  fileCount: 2,
  accept: "image/*,.pdf",
  multiple: true,
  files: [
    {
      name: "document.pdf",
      size: 204800,
      type: "application/pdf",
      lastModified: 1701234567890
    },
    {
      name: "photo.jpg",
      size: 512000,
      type: "image/jpeg",
      lastModified: 1701234567890
    }
  ],
  selectors: {
    id: "file-upload",
    name: "documents",
    label: "Upload Documents"
  }
}
```

## Supported File Input Types

### Standard File Input
```html
<input type="file" name="document" />
```

### Multiple Files
```html
<input type="file" name="photos" multiple />
```

### Accept Specific Types
```html
<input type="file" accept="image/*,.pdf" />
```

### With Label
```html
<label for="upload">Choose File</label>
<input type="file" id="upload" name="file" />
```

## Best Practices

### 1. Organize Fixtures
Keep test files organized:
```
cypress/fixtures/
  images/
    profile.jpg
    banner.png
  documents/
    contract.pdf
    invoice.pdf
  data/
    users.csv
    config.json
```

### 2. Use Meaningful Names
Name fixture files descriptively:
- ✅ `valid-passport-image.jpg`
- ✅ `test-invoice-2024.pdf`
- ❌ `file1.jpg`
- ❌ `test.pdf`

### 3. Keep Files Small
Use small files for faster test execution:
- Images: Use compressed JPEGs (< 100KB)
- PDFs: Use simple single-page documents
- CSVs: Use minimal sample data

### 4. Validate Upload
Add assertions after upload:
```javascript
cy.get('input[type="file"]').selectFile('cypress/fixtures/example.pdf')

// Verify file name appears
cy.contains('example.pdf').should('be.visible')

// Or verify upload success message
cy.contains('File uploaded successfully').should('be.visible')
```

### 5. Test Error Cases
Test upload restrictions:
```javascript
// Test file size limit
cy.get('input[type="file"]')
  .selectFile('cypress/fixtures/large-file.pdf')
cy.contains('File too large').should('be.visible')

// Test file type restriction
cy.get('input[type="file"][accept="image/*"]')
  .selectFile('cypress/fixtures/document.pdf')
cy.contains('Invalid file type').should('be.visible')
```

## Troubleshooting

### File Not Found
**Error:** `cy.selectFile() could not find the file`

**Solution:**
- Ensure file exists in `cypress/fixtures/` directory
- Check file name spelling and path
- Use relative path from fixtures folder

### Hidden Input
**Error:** `cy.selectFile() failed because this element is not visible`

**Solution:**
```javascript
cy.get('input[type="file"]').selectFile('file.pdf', { force: true })
```

### Multiple Files Not Working
**Issue:** Only first file is uploaded

**Solution:**
- Ensure input has `multiple` attribute
- Pass array of file paths:
```javascript
cy.get('input').selectFile(['file1.pdf', 'file2.jpg'])
```

### Drag and Drop Not Working
**Issue:** Drag and drop upload fails

**Solution:**
```javascript
cy.get('.dropzone').selectFile('file.pdf', { action: 'drag-drop' })
```

## Testing Checklist

When testing file uploads:
- [ ] Single file upload works
- [ ] Multiple file upload works
- [ ] File type validation works
- [ ] File size validation works
- [ ] Upload progress is shown
- [ ] Success message appears
- [ ] File preview is displayed
- [ ] Remove/cancel upload works
- [ ] Form submission includes files
- [ ] Error messages are clear

## Example Test Cases

### Test Case 1: Profile Picture Upload
```javascript
describe('Profile Picture Upload', () => {
  it('should upload profile picture', () => {
    cy.visit('/profile/edit')
    
    // Upload image
    cy.get('#profile-picture').selectFile('cypress/fixtures/profile.jpg')
    
    // Verify preview
    cy.get('.image-preview').should('be.visible')
    cy.get('.image-preview img').should('have.attr', 'src').and('include', 'profile.jpg')
    
    // Save profile
    cy.get('button[type="submit"]').click()
    
    // Verify success
    cy.contains('Profile updated successfully').should('be.visible')
  })
})
```

### Test Case 2: Multiple Documents Upload
```javascript
describe('Document Upload', () => {
  it('should upload multiple documents', () => {
    cy.visit('/documents/upload')
    
    // Upload multiple files
    cy.get('input[type="file"]').selectFile([
      'cypress/fixtures/contract.pdf',
      'cypress/fixtures/invoice.pdf',
      'cypress/fixtures/receipt.pdf'
    ])
    
    // Verify file list
    cy.contains('contract.pdf').should('be.visible')
    cy.contains('invoice.pdf').should('be.visible')
    cy.contains('receipt.pdf').should('be.visible')
    
    // Submit
    cy.get('.upload-button').click()
    
    // Verify upload
    cy.contains('3 files uploaded successfully').should('be.visible')
  })
})
```

### Test Case 3: Drag and Drop
```javascript
describe('Drag and Drop Upload', () => {
  it('should upload via drag and drop', () => {
    cy.visit('/upload')
    
    // Drag and drop file
    cy.get('.dropzone').selectFile('cypress/fixtures/document.pdf', {
      action: 'drag-drop'
    })
    
    // Verify dropzone feedback
    cy.get('.dropzone').should('have.class', 'file-added')
    cy.contains('document.pdf').should('be.visible')
  })
})
```

## Migration from cypress-file-upload

If you were using the old `cypress-file-upload` plugin:

**Before:**
```javascript
cy.get('input[type="file"]').attachFile('example.pdf')
```

**After (Modern Cypress):**
```javascript
cy.get('input[type="file"]').selectFile('cypress/fixtures/example.pdf')
```

The Cypress module automatically generates modern `cy.selectFile()` commands, so no manual migration needed!

## Resources

- [Cypress selectFile Documentation](https://docs.cypress.io/api/commands/selectfile)
- [Working with File Uploads](https://docs.cypress.io/api/commands/selectfile#Usage)
- [Fixtures Best Practices](https://docs.cypress.io/api/commands/fixture)

## Summary

The Cypress module's file upload support provides:
- ✅ Automatic detection of file inputs
- ✅ Complete file metadata capture
- ✅ Modern `cy.selectFile()` code generation
- ✅ Single and multiple file support
- ✅ Drag-and-drop compatibility
- ✅ Clear, maintainable test code

Start capturing file upload interactions today and generate production-ready Cypress tests automatically!
