const fs = require('fs');
const content = fs.readFileSync('\\\\192.168.60.194\\xampp\\xampp\\htdocs\\Ascencio_Connect\\resources\\views\\eventos\\sorteo.blade.php', 'utf8');

// Extract all <script> blocks
const scriptRegex = /<script>([\s\S]*?)<\/script>/g;
let match;
let i = 1;
while ((match = scriptRegex.exec(content)) !== null) {
    let scriptContent = match[1];
    // Replace Blade tags with empty strings or valid JS so it doesn't break acorn
    scriptContent = scriptContent.replace(/@json\((.*?)\)/g, '[]');
    scriptContent = scriptContent.replace(/\{\{.*?\}\}/g, '"blade_replaced"');
    
    try {
        require('acorn').parse(scriptContent, { ecmaVersion: 2020 });
        console.log(`Script block ${i} is valid.`);
    } catch (e) {
        console.error(`Syntax error in script block ${i}: ${e.message}`);
        // print the surrounding lines
        const lines = scriptContent.split('\n');
        const errLine = e.loc.line - 1;
        console.error('Line:', lines[errLine]);
    }
    i++;
}
