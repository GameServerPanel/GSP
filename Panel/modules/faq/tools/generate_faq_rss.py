import re, sys, glob, yaml, pathlib, xml.etree.ElementTree as ET
from html import escape

ROOT = pathlib.Path(__file__).resolve().parents[1]
RSS = ROOT / "FAQ.RSS"
DATA = ROOT / "data" / "games"

SECTION_TITLES = ["Config Files","Startup Parameters","Troubleshooting","Steam Workshop"]

def to_html_lines(title, blocks):
    # blocks is a list of (heading, list_of_lines) or raw strings
    out = []
    for b in blocks:
        if isinstance(b, tuple):
            heading, lines = b
            out.append(f"&lt;strong&gt;{escape(heading)}&lt;/strong&gt;&lt;br&gt;")
            for line in lines:
                out.append(f"- {escape(line)}&lt;br&gt;")
            out.append("&lt;br&gt;")
        else:
            out.append(escape(b) + "&lt;br&gt;")
    return "\n    ".join(out).rstrip()

def make_item_xml(title, category, html):
    return f"""<item>
  <title>{escape(title)}</title>
  <category>{escape(category)}</category>
  <content:encoded>
    {html}
  </content:encoded>
</item>"""

def parse_yaml(fp):
    with open(fp, "r", encoding="utf-8") as f:
        return yaml.safe_load(f)

def build_sections(g):
    sections = {}
    # Config Files
    cfg_lines = [f'{c["file"]} — {c.get("desc","")} Location(s): ' + ", ".join(c["paths"]) for c in g["configs"]]
    sections["Config Files"] = to_html_lines("Files and locations", cfg_lines)
    # Startup Parameters
    s = g["startup"]
    port_lines = [f'{p["label"]}: {p["relative"]} ({p["proto"]}) default {p["port"]}']
    port_lines += [f'{p["label"]}: {p["relative"]} ({p["proto"]}) default {p["port"]}' for p in s["ports"][1:]]
    flag_lines = [f'{f["flag"]} (default: {f.get("default","n/a")}) — {f.get("desc","")}' for f in s["flags"]]
    html = to_html_lines("Default command", [s["default_command"]])
    html += "\n    " + to_html_lines("Port scheme (relative to GP)", port_lines)
    html += "\n    " + to_html_lines("All parameters", flag_lines)
    sections["Startup Parameters"] = html
    # Troubleshooting
    sections["Troubleshooting"] = to_html_lines("Common issues & fixes", g["troubleshooting"])
    # Workshop
    if g.get("supports_workshop"):
        sections["Steam Workshop"] = to_html_lines("Workshop configuration", g["workshop"].get("notes",[]))
    return sections

def inject_items(rss_text, category, sections):
    # remove any existing items for this category+our titles
    for t in SECTION_TITLES:
        pat = re.compile(
            rf"<item>\s*<title>{re.escape(t)}</title>\s*<category>{re.escape(category)}</category>.*?</item>",
            flags=re.DOTALL)
        rss_text = re.sub(pat, "", rss_text)
    # insert before </channel>
    insertion = "\n".join([make_item_xml(t, category, sections[t]) for t in SECTION_TITLES if t in sections])
    return rss_text.replace("</channel>", insertion + "\n</channel>")

def main():
    rss_text = RSS.read_text(encoding="utf-8")
    for yml in sorted(glob.glob(str(DATA / "*.yml"))):
        g = parse_yaml(yml)
        sections = build_sections(g)
        rss_text = inject_items(rss_text, g["name"], sections)
    RSS.write_text(rss_text, encoding="utf-8")
    print("FAQ.RSS updated")

if __name__ == "__main__":
    main()

