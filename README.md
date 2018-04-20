# ortic forum

This repository contains a simple forum solution for concrete5 v8+ and PHP 7+.

Instead of using pages for everything, this forum uses a lean structure that should be more performant than a page +
attribute based solution. Each topic is a page in concrete5, but the actual message are stored in dedicated tables to make things faster and easier to handle.

## Installation

Copy the content of this repository to your packages directory and save everything in a folder called `ortic_forum`.
The package will create a new page type called `Forum`. Simply create a new page of that type to add a forum to your sitemap.
