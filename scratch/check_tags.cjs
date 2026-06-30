
const fs = require('fs');
const content = fs.readFileSync('resources/views/dashboard/partials/_department-leader.blade.php', 'utf8');

let stack = [];
let lines = content.split('\n');

lines.forEach((line, i) => {
    let divRegex = /<(div)|<\/div>/g;
    let match;
    while ((match = divRegex.exec(line)) !== null) {
        if (match[1] === 'div') {
            // Find the full tag for context
            let fullLine = line.substring(match.index, line.indexOf('>', match.index) + 1);
            stack.push({ line: i + 1, tag: fullLine });
        } else {
            if (stack.length === 0) {
                console.log(`Extra closing div on line ${i + 1}`);
            } else {
                stack.pop();
            }
        }
    }
});

if (stack.length > 0) {
    console.log("Unclosed divs:");
    stack.forEach(item => {
        console.log(`Line ${item.line}: ${item.tag}`);
    });
} else {
    console.log("All divs are balanced!");
}
