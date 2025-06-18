<h2>History Dokumen</h2>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Nomor Dokumen</th>
            <th>Kategori</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($documents as $doc)
            <tr>
                <td>{{ $doc->name }}</td>
                <td>{{ $doc->document_number }}</td>
                <td>{{ $doc->template->category->name }}</td>
                <td>{{ $doc->approval->status ?? 'Belum ada' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
