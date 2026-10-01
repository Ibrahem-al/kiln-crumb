# Your part: the Figma → WordPress step (about 2–3 hours)

The theme covers WordPress, performance, accessibility, SEO and QA. The one gap it can't cover is **Figma Dev Mode**, because that has to be you, in Figma. This is also the exact workflow the job describes, so doing it once gives you a true answer to their application question.

## 1. Get Figma with Dev Mode (free as a student)
Sign up for Figma's **education plan** with your GWU email. Check that Dev Mode is included on your plan when you sign up.

## 2. Set up the design tokens as Figma variables (15 min)
Create a Figma file "Kiln & Crumb" and add these as **color variables** so Dev Mode shows token names instead of hex codes:

| Variable | Value | theme.json slug |
|---|---|---|
| cream | #FBF5EC | cream |
| paper | #F2E6D3 | paper |
| ink | #2A1F18 | ink |
| muted | #5E4E43 | muted |
| crust | #8C3B14 | crust |
| sage | #4B5A41 | sage |
| butter | #F0C96A | butter |
| line | #D9C8B0 | line |

Add text styles: Fraunces 500 for headings, Inter 400 for body.

## 3. Design ONE new section in Figma (1 hour)
Pick something the site doesn't have yet, e.g. **"Custom cakes & catering"**: a heading, short intro, three cards (Birthday cakes / Office breakfast / Wedding bread table) with a line of text and a starting price each, and a button. Design a desktop frame (1440 wide) and a mobile frame (390 wide). You can also try generating a first draft with Figma Make, then clean it up yourself.

## 4. Build it from Dev Mode with Claude Code (1 hour)
1. Open the frame in Dev Mode. Note the spacing, the variables used, and the type styles.
2. Optional but worth it: connect Figma's MCP server to Claude Code so Claude can read the selected frame directly.
3. Ask Claude Code to build it as a new pattern, `patterns/catering.php`, using **only theme.json tokens** (no hard-coded hex values or pixel font sizes), and to add it to `templates/front-page.html`.
4. Run `npm run qa` in `qa/`. Fix whatever it finds.
5. Compare the result side by side with your Figma frame at 390 and 1440 wide, and note what the AI got wrong. **That list is your interview answer.**

## 5. Then update your resume bullet
Once it's real, change the Kiln & Crumb bullet to start:
> "Designed a new catering section in Figma and built it from Dev Mode with Claude Code as a block pattern using only theme.json tokens; ..."

And add **Figma (Dev Mode, variables)** to your skills line.
