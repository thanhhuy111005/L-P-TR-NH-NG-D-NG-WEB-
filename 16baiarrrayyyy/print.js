// Hàm print: in kết quả ra trang web (và cả console)
function print(value) {
  var text = typeof value === "string" ? value : JSON.stringify(value);
  if (text === undefined) text = String(value);
  var out = document.getElementById("output");
  if (out) out.textContent += text + "\n";
  console.log(value);
}
