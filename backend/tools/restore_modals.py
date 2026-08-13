import re

# Read original
with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'r', encoding='utf-8') as f:
    dashboard_content = f.read()

# Read backup
with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php.bak', 'r', encoding='utf-8') as f:
    bak_content = f.read()

# Extract from bak
modals_match = re.search(r'(<!-- Modal Bảng xếp hạng -->.*?</div>\s*</div>\s*</div>\s*)</div>\s*@section', bak_content, re.DOTALL)
if modals_match:
    modals_html = modals_match.group(1)
else:
    print("Modals not found in bak")
    modals_html = ""

buttons_match = re.search(r'(<!-- Floating buttons -->.*?</div>\s*)<script>', bak_content, re.DOTALL)
if buttons_match:
    buttons_html = buttons_match.group(1)
else:
    print("Buttons not found in bak")
    buttons_html = ""

scripts_match = re.search(r'(// Initialize Tooltips.*?</script>\s*<style>.*?</style>)', bak_content, re.DOTALL)
if scripts_match:
    scripts_html = scripts_match.group(1)
else:
    print("Scripts not found in bak")
    scripts_html = ""

# Inject Modals before @section('script')
# In dashboard.blade.php, it ends with:
#     </div>
# </div>
# @section('script')
dashboard_content = re.sub(r'(    </div>\s*</div>\s*@section\(\'script\'\))', modals_html + r'\1', dashboard_content, count=1)

# Inject Floating Buttons and Scripts inside @section('script')
# We can inject them right before the closing </script> tag of exportToCSV, or after the <script src="...apexcharts.min.js"></script>

dashboard_content = dashboard_content.replace(
    "@section('script')\n",
    "@section('script')\n" + buttons_html + "\n<script>\n" + scripts_html.replace('</script>\n    <style>', '</script>\n<style>') + "\n"
)

# Fix double script tags if any issues occurred (just simple replacement)
dashboard_content = dashboard_content.replace('<script>\n<script>', '<script>')

with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(dashboard_content)

print("Restored modals and buttons successfully")
