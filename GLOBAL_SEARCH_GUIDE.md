# Global Search System

## 🎯 Overview
AI-powered live search system for Projects, Modules, and Test Cases with fuzzy matching, keyboard shortcuts, and smart ranking.

## ✨ Features

### Search Capabilities
- **Real-time search** - Results appear as you type (300ms debounce)
- **Fuzzy matching** - Finds "loign" when you search for "login"
- **Smart ranking** - Most relevant results first
- **Grouped results** - Organized by Projects, Modules, Test Cases
- **Highlight matches** - Bold yellow highlight on matched text
- **Keyboard navigation** - ↑↓ to navigate, Enter to select, Esc to close

### Keyboard Shortcuts
- `Ctrl + K` (Windows) or `Cmd + K` (Mac) - Focus search box
- `↑` / `↓` - Navigate through results
- `Enter` - Open selected result
- `Esc` - Close dropdown

### Search Behavior
- Minimum 2 characters to start search
- Shows loading spinner during search
- Maximum results: 5 projects, 5 modules, 7 test cases
- Permission-based (only shows user's accessible items)

## 🏗️ Architecture

### Backend (Laravel)
```
app/Modules/Setting/
├── Http/Controllers/
│   └── SearchController.php       # API endpoint controller
├── Services/
│   └── SearchService.php          # Search logic & ranking
└── routes/
    └── web.php                    # API route registration
```

### API Endpoint
```
GET /api/search?q={query}
```

**Response:**
```json
{
  "success": true,
  "query": "user login",
  "results": {
    "projects": [...],
    "modules": [...],
    "test_cases": [...]
  },
  "total": 15
}
```

### Frontend (Alpine.js)
- Component: `globalSearch()` in header.blade.php
- Real-time search with debouncing
- Keyboard navigation support
- Responsive dropdown UI

## 🎨 UI/UX Design

### Dropdown Structure
```
┌─────────────────────────────────┐
│ 🔍 Search input                 │
├─────────────────────────────────┤
│ 🗂️ PROJECTS (2)                 │
│   Project Name                  │
│   Description • 5 modules       │
├─────────────────────────────────┤
│ 📦 MODULES (3)                  │
│   Module Name → Project Name    │
│   5 test cases                  │
├─────────────────────────────────┤
│ ✅ TEST CASES (5)               │
│   Test Case Name                │
│   Module → Project • 3 events   │
├─────────────────────────────────┤
│ Found 10 results                │
└─────────────────────────────────┘
```

## 🔍 Relevance Algorithm

### Scoring System
- **Exact match in name**: +100 points
- **Starts with query**: +50 points
- **Contains query in name**: +30 points
- **Contains in description**: +10 points
- **Word match bonus**: +5 points per word

Results are sorted by relevance score (highest first).

## 🚀 Usage

### For Users
1. Press `Ctrl + K` or click search box
2. Type at least 2 characters
3. Navigate with arrow keys or mouse
4. Press Enter or click to open result

### For Developers
The search system automatically works with existing permissions. No additional configuration needed.

## 🔒 Security

- Authentication required (`auth` middleware)
- Permission-based results (users only see their own items)
- SQL injection protection (parameterized queries)
- XSS protection (escaped output)

## 📊 Performance

- **Debounced requests**: 300ms delay prevents excessive API calls
- **Limited results**: Max 17 results per query
- **Optimized queries**: Uses `select()` to fetch only needed columns
- **Indexed searches**: Uses LIKE queries (can be upgraded to FULLTEXT)

## 🎯 Future Enhancements

1. **Recent searches** - Show last 10 searches
2. **Search history** - Store in localStorage
3. **Advanced filters** - Filter by status, date range
4. **Search analytics** - Track popular searches
5. **Elasticsearch** - For very large datasets (1M+ records)

## 🐛 Troubleshooting

### Search not working
```bash
php artisan route:clear
php artisan config:clear
```

### No results showing
- Check database has data
- Verify user has `created_by` set correctly
- Check browser console for errors

### Dropdown not appearing
- Ensure Alpine.js is loaded
- Check for JavaScript console errors
- Verify x-data="globalSearch()" is present

## 📝 Example Queries

- `login` - Finds "Login Module", "User Login Test"
- `btb` - Finds "BTB Project"
- `test case` - Finds all test cases with "test" or "case" in name
- `user reg` - Finds "User Registration" (partial match)
