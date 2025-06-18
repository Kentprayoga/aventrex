<h2>Detail Dokumen</h2>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th>Nama</th>
        <td>{{ $document->name }}</td>
    </tr>
    <tr>
        <th>Nomor Dokumen</th>
        <td>{{ $document->document_number }}</td>
    </tr>
    <tr>
        <th>Kategori</th>
        <td>{{ $document->template->category->name }}</td>
    </tr>
    <tr>
        <th>Template</th>
        <td>{{ $document->template->name }}</td>
    </tr>
    <tr>
        <th>Tanggal Pengajuan</th>
        <td>{{ $document->tanggal_pengajuan }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ $document->approval->status ?? 'Belum ada' }}</td>
    </tr>
    <tr>
        <th>Alasan</th>
        <td>{{ $document->alasan }}</td>
    </tr>
</table>
