<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Web Crawler - Extract Custom Elements</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
            font-family: 'Courier New', monospace;
        }
        .help-text {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .examples {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 13px;
        }
        .examples strong {
            color: #667eea;
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 35px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        #loading {
            display: none;
            color: #667eea;
            margin-top: 20px;
            font-weight: 600;
        }
        #results {
            margin-top: 30px;
        }
        .result-item {
            background: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid #667eea;
        }
        .result-item.failed {
            border-left-color: #f44336;
        }
        .result-url {
            font-weight: bold;
            color: #667eea;
            word-break: break-all;
            font-size: 16px;
        }
        .result-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        .result-section h4 {
            margin: 0 0 10px 0;
            color: #555;
            font-size: 14px;
            font-weight: 600;
        }
        .result-title {
            margin-top: 8px;
            color: #333;
            font-weight: 600;
        }
        .result-meta {
            margin-top: 5px;
            font-size: 13px;
            color: #999;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: #667eea;
            color: white;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .custom-element {
            background: white;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }
        .element-text {
            color: #333;
            margin-bottom: 5px;
        }
        .element-selector {
            font-family: 'Courier New', monospace;
            color: #667eea;
            font-size: 12px;
            font-weight: bold;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            flex: 1;
            min-width: 150px;
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🕷️ Web Crawler</h1>
        <p class="subtitle">Extract custom elements from any website using CSS selectors</p>

        <form id="crawlerForm">
            <div class="form-group">
                <label for="url">🌐 Website URL:</label>
                <input type="text" id="url" name="url" placeholder="https://example.com" required>
            </div>

            <div class="form-group">
                <label for="elements">🎯 Elements to Extract (CSS Selectors):</label>
                <textarea id="elements" name="elements" placeholder="h1, .price, #product-name, article p"></textarea>
                <div class="help-text">Enter CSS selectors separated by commas. Leave empty to extract default elements (headings, images, links).</div>
                <div class="examples">
                    <strong>Examples:</strong><br>
                    • <code>h1, h2, h3</code> - Extract all headings<br>
                    • <code>.product-price</code> - Extract product prices<br>
                    • <code>#main-content p</code> - Extract paragraphs from main content<br>
                    • <code>article</code> - Extract article content<br>
                    • <code>.author-name, .publish-date</code> - Extract author and date
                </div>
            </div>

            <div class="form-group">
                <label for="max_depth">📊 Maximum Depth (1-5):</label>
                <input type="number" id="max_depth" name="max_depth" value="2" min="1" max="5">
                <div class="help-text">How many levels deep to crawl from the starting URL</div>
            </div>

            <div class="form-group">
                <label for="max_urls">🔢 Maximum URLs (1-50):</label>
                <input type="number" id="max_urls" name="max_urls" value="10" min="1" max="50">
                <div class="help-text">Maximum number of pages to crawl</div>
            </div>

            <button type="submit" id="submitBtn">🚀 Start Crawling</button>
        </form>

        <div id="loading">
            <p>⏳ Crawling in progress... This may take a while.</p>
        </div>

        <div id="error" class="error" style="display: none;"></div>

        <div id="results"></div>
    </div>

    <script>
        document.getElementById('crawlerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const resultsDiv = document.getElementById('results');
            const errorDiv = document.getElementById('error');

            // Clear previous results
            resultsDiv.innerHTML = '';
            errorDiv.style.display = 'none';

            // Show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';

            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('{{ route("crawler.crawl") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    displayResults(result);
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('An error occurred: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                loading.style.display = 'none';
            }
        });

        function displayResults(data) {
            const resultsDiv = document.getElementById('results');
            
            let html = `
                <div class="stats">
                    <div class="stat-box">
                        <div class="stat-number">${data.total_crawled}</div>
                        <div class="stat-label">Pages Crawled</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">${countSuccessful(data.results)}</div>
                        <div class="stat-label">Successful</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">${countFailed(data.results)}</div>
                        <div class="stat-label">Failed</div>
                    </div>
                </div>
                <h2>Crawl Results</h2>
            `;
            
            data.results.forEach((result, index) => {
                const isFailed = result.status === 'failed';
                html += `
                    <div class="result-item ${isFailed ? 'failed' : ''}" id="result-${index}">
                        <div class="result-url">🔗 ${result.url}</div>
                        ${!isFailed ? `
                            ${result.title ? `<div class="result-title">📄 ${escapeHtml(result.title)}</div>` : ''}
                            ${result.description ? `<div class="result-meta">${escapeHtml(result.description)}</div>` : ''}
                            
                            ${displayHeadings(result.headings)}
                            ${displayImages(result.images)}
                            ${displayLinks(result.links)}
                            ${displayCustomElements(result.custom_elements)}
                            
                            <div class="result-meta" style="margin-top: 15px;">
                                ✅ Status: ${result.status} | 
                                🕒 ${result.crawled_at} | 
                                📍 Found on: ${result.found_on}
                            </div>
                        ` : `
                            <div class="result-meta" style="color: #c62828; margin-top: 10px;">
                                ❌ Failed: ${escapeHtml(result.error)}
                            </div>
                        `}
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
        }

        function displayHeadings(headings) {
            if (!headings) return '';
            
            let html = '';
            let hasHeadings = false;
            
            ['h1', 'h2', 'h3'].forEach(tag => {
                if (headings[tag] && headings[tag].length > 0) {
                    hasHeadings = true;
                }
            });
            
            if (hasHeadings) {
                html += '<div class="result-section"><h4>📝 Headings:</h4>';
                ['h1', 'h2', 'h3'].forEach(tag => {
                    if (headings[tag] && headings[tag].length > 0) {
                        headings[tag].forEach(text => {
                            html += `<span class="badge">${tag.toUpperCase()}</span> ${escapeHtml(text)}<br>`;
                        });
                    }
                });
                html += '</div>';
            }
            
            return html;
        }

        function displayImages(images) {
            if (!images || images.length === 0) return '';
            
            return `
                <div class="result-section">
                    <h4>🖼️ Images (${images.length}):</h4>
                    ${images.slice(0, 5).map(img => 
                        `<div class="result-meta">• ${escapeHtml(img.alt || 'No alt text')}</div>`
                    ).join('')}
                    ${images.length > 5 ? `<div class="result-meta">... and ${images.length - 5} more</div>` : ''}
                </div>
            `;
        }

        function displayLinks(links) {
            if (!links || links.length === 0) return '';
            
            return `
                <div class="result-section">
                    <h4>🔗 Links Found: ${links.length}</h4>
                </div>
            `;
        }

        function displayCustomElements(customElements) {
            if (!customElements || Object.keys(customElements).length === 0) return '';
            
            let html = '<div class="result-section"><h4>🎯 Custom Elements:</h4>';
            
            for (const [selector, elements] of Object.entries(customElements)) {
                html += `<div class="element-selector">${escapeHtml(selector)} (${elements.length} found)</div>`;
                
                elements.slice(0, 3).forEach(elem => {
                    html += `
                        <div class="custom-element">
                            <div class="element-text">${escapeHtml(elem.text || elem.html || 'No content')}</div>
                        </div>
                    `;
                });
                
                if (elements.length > 3) {
                    html += `<div class="result-meta">... and ${elements.length - 3} more</div>`;
                }
            }
            
            html += '</div>';
            return html;
        }

        function countSuccessful(results) {
            return results.filter(r => r.status !== 'failed').length;
        }

        function countFailed(results) {
            return results.filter(r => r.status === 'failed').length;
        }

        function showError(message) {
            const errorDiv = document.getElementById('error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
