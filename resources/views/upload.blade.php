<form action="{{ route('generate.pdf') }}" method="POST" enctype="multipart/form-data" class="p-10">
    @csrf
    <div class="mb-4">
        <label>1. Davinci Picture:</label>
        <input type="file" name="davinci_img" required class="block border p-2">
    </div>
    <div class="mb-4">
        <label>2. Jibble Picture:</label>
        <input type="file" name="jibble_img" required class="block border p-2">
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Generate PDF Report</button>
</form>