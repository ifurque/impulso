import { copyFile, mkdir, readdir, rm } from 'node:fs/promises';
import { join } from 'node:path';

const sourceDirectory = 'public';
const outputDirectory = 'vercel-output';

await rm(outputDirectory, { recursive: true, force: true });
await mkdir(outputDirectory, { recursive: true });

async function copyPublicAssets(source, destination) {
    for (const entry of await readdir(source, { withFileTypes: true })) {
        if (entry.isSymbolicLink() || entry.name === 'index.php' || entry.name === '.htaccess') {
            continue;
        }

        const sourcePath = join(source, entry.name);
        const destinationPath = join(destination, entry.name);

        if (entry.isDirectory()) {
            await mkdir(destinationPath, { recursive: true });
            await copyPublicAssets(sourcePath, destinationPath);
        } else if (entry.isFile()) {
            await copyFile(sourcePath, destinationPath);
        }
    }
}

await copyPublicAssets(sourceDirectory, outputDirectory);
