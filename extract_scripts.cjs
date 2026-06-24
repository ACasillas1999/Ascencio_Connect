const fs = require('fs');
const content = fs.readFileSync('\\\\192.168.60.194\\xampp\\xampp\\htdocs\\Ascencio_Connect\\resources\\views\\eventos\\sorteo.blade.php', 'utf8');

// Extract all <script> blocks
const scriptRegex = /<script>([\s\S]*?)<\/script>/g;
let match;
let i = 1;
while ((match = scriptRegex.exec(content)) !== null) {
    let scriptContent = match[1];
    scriptContent = scriptContent.replace(/@json\((.*?)\)/g, '[]');
    scriptContent = scriptContent.replace(/\{\{.*?\}\}/g, '"blade_replaced"');
    fs.writeFileSync(`\\\\192.168.60.194\\xampp\\xampp\\htdocs\\Ascencio_Connect\\script_${i}.js`, scriptContent);
    i++;
}
console.log('Scripts extracted.');
