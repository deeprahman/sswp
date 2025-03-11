const fs = require('fs');
const path = require('path');

// Configuration constants
const PREFIX = 'sswp'; // The prefix to add
const TARGET_PATH = process.argv[2] || '.'; // Path from command line or current directory

// Regular expressions for matching PHP variables, classes, and functions
const variableRegex = /\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/g;
const classRegex = /class\s+([A-Z][a-zA-Z0-9_]*)/g;
const classExtendRegex = /extends\s+([A-Z][a-zA-Z0-9_]*)/g;
const classImplementsRegex = /implements\s+([A-Z][a-zA-Z0-9_]*)/g;
const classInstantiationRegex = /new\s+([A-Z][a-zA-Z0-9_]*)/g;
const classTypeHintRegex = /\(\s*([A-Z][a-zA-Z0-9_]*)\s*\$|\,\s*([A-Z][a-zA-Z0-9_]*)\s*\$/g;
const functionRegex = /function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/g;
const functionCallRegex = /([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(/g;

// Store original to prefixed names for consistency
const renamedVariables = new Map();
const renamedClasses = new Map();
const renamedFunctions = new Map();

// Check if a name already has the prefix
const hasPrefix = (name, prefix) => {
  return name.toLowerCase().startsWith(prefix.toLowerCase() + '_');
};

// Apply prefix to variables
const prefixVariable = (match, name) => {
  // Skip if it's already prefixed or if it's a superglobal
  const superglobals = ['_GET', '_POST', '_COOKIE', '_SESSION', '_FILES', '_SERVER', '_ENV', '_REQUEST', 'GLOBALS', 'this'];
  if (hasPrefix(name, PREFIX) || superglobals.includes(name)) {
    return match;
  }

  // Check if we've seen this variable before
  if (renamedVariables.has(name)) {
    return `$${renamedVariables.get(name)}`;
  }

  const prefixed = `${PREFIX}_${name}`;
  renamedVariables.set(name, prefixed);
  return `$${prefixed}`;
};

// Apply prefix to classes
const prefixClass = (match, name) => {
  if (hasPrefix(name, PREFIX)) {
    return match;
  }

  // Check if we've seen this class before
  if (renamedClasses.has(name)) {
    return match.replace(name, renamedClasses.get(name));
  }

  // Format: First letter of prefix capitalized + original class name
  const prefixed = `${PREFIX.charAt(0).toUpperCase() + PREFIX.slice(1)}_${name}`;
  renamedClasses.set(name, prefixed);
  return match.replace(name, prefixed);
};

// Apply prefix to functions
const prefixFunction = (match, name) => {
  // Skip if it's already prefixed or a PHP built-in function
  if (hasPrefix(name, PREFIX)) {
    return match;
  }

  // Check if we've seen this function before
  if (renamedFunctions.has(name)) {
    return match.replace(name, renamedFunctions.get(name));
  }

  const prefixed = `${PREFIX}_${name}`;
  renamedFunctions.set(name, prefixed);
  return match.replace(name, prefixed);
};

// Process a single PHP file
const processPhpFile = (filePath) => {
  console.log(`Processing file: ${filePath}`);
  try {
    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;
    let newContent = content;

    // Replace variables
    const tempContent1 = newContent.replace(variableRegex, prefixVariable);
    if (tempContent1 !== newContent) {
      newContent = tempContent1;
      modified = true;
    }

    // Replace classes and their usage
    const tempContent2 = newContent.replace(classRegex, prefixClass);
    if (tempContent2 !== newContent) {
      newContent = tempContent2;
      modified = true;
    }
    
    // Replace extends
    const tempContent3 = newContent.replace(classExtendRegex, (match, name) => {
      if (renamedClasses.has(name)) {
        return match.replace(name, renamedClasses.get(name));
      }
      return match;
    });
    if (tempContent3 !== newContent) {
      newContent = tempContent3;
      modified = true;
    }
    
    // Replace implements
    const tempContent4 = newContent.replace(classImplementsRegex, (match, name) => {
      if (renamedClasses.has(name)) {
        return match.replace(name, renamedClasses.get(name));
      }
      return match;
    });
    if (tempContent4 !== newContent) {
      newContent = tempContent4;
      modified = true;
    }
    
    // Replace new instantiations
    const tempContent5 = newContent.replace(classInstantiationRegex, (match, name) => {
      if (renamedClasses.has(name)) {
        return match.replace(name, renamedClasses.get(name));
      }
      return match;
    });
    if (tempContent5 !== newContent) {
      newContent = tempContent5;
      modified = true;
    }
    
    // Replace type hints
    const tempContent6 = newContent.replace(classTypeHintRegex, (match, name1, name2) => {
      const name = name1 || name2;
      if (renamedClasses.has(name)) {
        return match.replace(name, renamedClasses.get(name));
      }
      return match;
    });
    if (tempContent6 !== newContent) {
      newContent = tempContent6;
      modified = true;
    }

    // Replace functions
    const tempContent7 = newContent.replace(functionRegex, prefixFunction);
    if (tempContent7 !== newContent) {
      newContent = tempContent7;
      modified = true;
    }
    
    // Replace function calls
    const tempContent8 = newContent.replace(functionCallRegex, (match, name) => {
      if (renamedFunctions.has(name)) {
        return match.replace(name, renamedFunctions.get(name));
      }
      return match;
    });
    if (tempContent8 !== newContent) {
      newContent = tempContent8;
      modified = true;
    }

    // Save changes if the file was modified
    if (modified) {
      fs.writeFileSync(filePath, newContent, 'utf8');
      console.log(`✓ Updated: ${filePath}`);
    } else {
      console.log(`✓ No changes needed: ${filePath}`);
    }
  } catch (error) {
    console.error(`Error processing file ${filePath}:`, error);
  }
};

// Recursively traverse the directory
const traverseDirectory = (dirPath) => {
  const files = fs.readdirSync(dirPath);
  
  files.forEach(file => {
    const filePath = path.join(dirPath, file);
    const stats = fs.statSync(filePath);
    
    if (stats.isDirectory()) {
      traverseDirectory(filePath);
    } else if (stats.isFile() && path.extname(filePath).toLowerCase() === '.php') {
      processPhpFile(filePath);
    }
  });
};

// Check if the path is a PHP file
const isPhpFile = (filePath) => {
  return path.extname(filePath).toLowerCase() === '.php';
};

// Main execution
console.log(`Starting PHP prefix renaming with prefix: ${PREFIX}`);
console.log(`Target path: ${TARGET_PATH}`);

try {
  const stats = fs.statSync(TARGET_PATH);
  
  if (stats.isDirectory()) {
    // Process directory
    console.log(`Processing directory: ${TARGET_PATH}`);
    traverseDirectory(TARGET_PATH);
  } else if (stats.isFile() && isPhpFile(TARGET_PATH)) {
    // Process individual PHP file
    console.log(`Processing single file: ${TARGET_PATH}`);
    processPhpFile(TARGET_PATH);
  } else if (stats.isFile() && !isPhpFile(TARGET_PATH)) {
    console.error('The provided file is not a PHP file.');
  } else {
    console.error('The provided path is neither a directory nor a file.');
  }
  
  // Summary
  console.log('\nRenaming Summary:');
  console.log(`Variables renamed: ${renamedVariables.size}`);
  console.log(`Classes renamed: ${renamedClasses.size}`);
  console.log(`Functions renamed: ${renamedFunctions.size}`);
  console.log('\nProcess completed!');
  
} catch (error) {
  console.error('Error:', error);
}