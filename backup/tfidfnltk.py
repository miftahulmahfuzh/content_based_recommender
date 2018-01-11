# -*- coding: utf-8 -*-
from __future__ import division, unicode_literals
import math, operator
from textblob import TextBlob
from scipy import spatial

def tf(word, blob):
    return blob.words.count(word) / len(blob.words)

def n_containing(word, bloblist):
    return sum(1 for blob in bloblist if word in blob.words)

def idf(word, bloblist):
    return math.log(len(bloblist) / (1 + n_containing(word, bloblist)))

def tfidf(word, blob, bloblist):
    return tf(word, blob) * idf(word, bloblist) 

def preprocess(bloblist, stopwords):
    bloblist = [blob.lower() for blob in bloblist]
    for blob in bloblist:
        list_temp = [word for word in blob.words if word not in stopwords]
        blob = TextBlob(' '.join(list_temp))
    return bloblist

stopwords = ['href', 'class', 'title', 'blue', 'div', 'http']

article1 = TextBlob("""TRIBUNNEWS.COM, JAKARTA - <a href=""http://www.tribunnews.com/tag/pkpu-human-initiative"" class=""blue"" title=""PKPU Human Initiative"">PKPU Human Initiative</a> mensupport program 13 yayasan yatim berupa program kursus bahasa Inggris bagi anak-anak yatim. Program <em>English Corner For Children</em> ini diperuntukan secara gratis bagi anak-anak yatim dhuafa di wilayah Jabodebek.  Supporting program diawali event<em> soft launching</em> dan penandatanganan Surat Perjanjian Kerjasama program di Gedung Pendayagunaan PKPU Pusat, Jumat (3/3/2017). Acara itu dihadiri 13 yayasan mitra terpilih dengan jumlah total bantuan senilai Rp 200 juta. Sumarna, Manager Orphan PKPU Jakarta mengatakan, program ini bertitik tolak dari tantangan dunia akan penguasaan Bahasa Inggris di era persaingan global. "Sedangkan di sisi lain anak-anak mengalami kesulitan dalam mengakses pendalaman Bahasa Inggris karena biaya yang cukup tinggi," katanya, Kamis. Dikatakannya, anak-anak yatim harus bisa menjawab <a href="http://www.tribunnews.com/tag/tantangan-global" class="blue" title="tantangan global">tantangan global</a> melalui kemampuannya dalam Bahasa Inggris dan tidak perlu khawatir soal biaya karena ini gratis. Menurutnya, program ini akan menjadi cikal bakal pusat kegiatan yatim berdaya saat mereka akan diberi edukasi berupa peningkatan bahasa Inggris. Juga mendapatkan pendampingan akademik, keterampilan, pembinaan karakter dan skill-skill terapan lainnya dalam bentuk.""")

article2 = TextBlob("""TRIBUNNEWS.COM, JAKARTA - Ternyata melatih kemampuan otak tengah bisa membuat seseorang menjadi jenius. Mengapa? Karena otak tengah yang merupakan bagian terkecil dari otak ini berfungsi sebagai stasiun relai bagi indera pendengaran dan penglihatan. Nah, ada seorang anak usianya baru 12 tahun, tetapi memiliki kemampuan otak tengah yang sangat terasah. Namanya <a href=""http://www.tribunnews.com/tag/ryan-khemacaro"" class=""blue"" title=""Ryan Khemacaro"">Ryan Khemacaro</a>.  Menurut Ryan, melatih otak tengah itu dapat menambah konsentrasi dalam mengerjakan sesuatu, bisa meningkatkan intuisi, pendengaran, penciuman, penglihatan. Cara melatih otak tengah, kata Ryan, satu di antaranya adalah senam otak. Sapa Indonesia di <a href=""http://www.tribunnews.com/tag/kompas-tv"" class=""blue"" title=""Kompas TV"">Kompas TV</a> mengupas kemampuan Ryan melatih otak tengahnya.""")

article3 = TextBlob("""TRIBUNNEWS.COM, JAKARTA - Sekretaris Dinas Pendidikan DKI Jakarta Susi Nurhati mengatakan pihaknya meninjau ulang izin operasional <a href=""http://www.tribunnews.com/tag/smk-budi-murni-4"" class=""blue"" title=""SMK Budi Murni 4"">SMK Budi Murni 4</a>, SMK Adi Luhur 2, dan <a href=""http://www.tribunnews.com/tag/smk-bunda-kandung"" class=""blue"" title=""SMK Bunda Kandung"">SMK Bunda Kandung</a>, usai sejumlah siswanya terlibat <a href=""http://www.tribunnews.com/tag/tawuran"" class=""blue"" title=""tawuran"">tawuran</a> yang menewaskan satu orang di flyover Pasar Rebo, Selasa (14/2/2017). ""Pihak Dinas Pendidikan akan meninjau ulang terhadap Izin Operasional sekolah,"" kata Susi dalam keterangan tertulisnya, Minggu (26/2/2017). Selain meninjau izin operasional sekolah, Susi mengatakan, pihaknya juga akan berkoordinasi dengan Badan Akresitasi Provinsi untuk meninjau ulang akreditasi sekolah. Sebab, bukan kali ini saja siswa-siswa sekolah yang disebutkan itu terlibat dalam <a href=""http://www.tribunnews.com/tag/tawuran"" class=""blue"" title=""tawuran"">tawuran</a>. Dalam <a href=""http://www.tribunnews.com/tag/tawuran"" class=""blue"" title=""tawuran"">tawuran</a> 14 Februari 2017 kemarin, seorang siswa Teknik Mesin kelas IX, Ahmad Andika Bagaskara, tewas di tempat. Susi mengatakan ada 17 siswa <a href=""http://www.tribunnews.com/tag/smk-budi-murni-4"" class=""blue"" title=""SMK Budi Murni 4"">SMK Budi Murni 4</a> dan 6 siswa <a href=""http://www.tribunnews.com/tag/smk-bunda-kandung"" class=""blue"" title=""SMK Bunda Kandung"">SMK Bunda Kandung</a> yang siang itu berkelahi melawan 18 siswa SMK Adi Luhur 2. Siswa yang terlibat langsung sudah dikeluarkan, sementara yang tidak terlibat langsung dicabut Kartu Jakarta Pintar (KJP)-nya. Polisi saat ini baru mengamankan satu orang, dan masih memburu tiga lainnya yang diduga terlibat dalam menyebabkan tewasnya Andika. Sejumlah siswa lainnya diperiksa polisi.""")

article4 = TextBlob("""TRIBUNNEWS.COM, SERANG - Ketua <a href=""http://www.tribunnews.com/tag/mpr-ri"" class=""blue"" title=""MPR RI"">MPR RI</a>, <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a> melanjutkan safari kebangsaan di Banten dengan Silaturrahmi ke Pondok Pesantren Attaufiqiyah di Kampung Lapang, Sukamanah, Baros Kabupaten Serang, Selasa (28/2/2017). Silaturrahmi Ketua MPR ini sekaligus merupakan tasyakuran peringatan hari lahir ke-91 Nahdlatul Ulama (NU). Kedatangan <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a> disambut langsung oleh Pimpinan Ponpes KH. Edy Suhrowardi, Ketua PC NU Serang KH. Moh. Kholil dan jajaran pengurus NU lainnya. Hadir mendampingi Ketua MPR, Sekretaris Fraksi PAN DPR RI <a href=""http://www.tribunnews.com/tag/yandri-susanto"" class=""blue"" title=""Yandri Susanto"">Yandri Susanto</a>. Zulkifli Hasan mengungkapkan, bahwa Santri adalah salah satu pilar kemerdekaan bangsa Indonesia. ""Banggalah menjadi santri. Sejarah mencatat dengan tinta emas peran santri dalam perjuangan republik merebut kemerdekaan,"" kata <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a> yang langsung disambut tepuk tangan 700 lebih santri At Taufiqiyah Zulkifli Hasan mengingatkan bahwa di era reformasi ini, santri harus terus menunjukkan perannya mengisi kemerdekaan. ""Tugas santri hari ini adalah menularkan semangat cinta agama dan cinta tanah air pada anak bangsa lainnya,"" kata <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a>. Di sisi lain, <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a> juga mengingatkan bahwa di era persaingan bebas ini, santri harus belajar sungguh sungguh dan bekerja keras. ""Tidak ada jalan sukses yang mudah. Semuanya harus ditempuh dengan kerja keras. Berjuanglah raih cita cita, buat keluarga di kampung bangga,"" tutur <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a>. Di sela sela pidato, <a href=""http://www.tribunnews.com/tag/zulkifli-hasan"" class=""blue"" title=""Zulkifli Hasan"">Zulkifli Hasan</a> menyelipkan pertanyaan mengenai Empat Pilar MPR. Santri sangat antusias karena yang dapat menjawab diberi hadiah uang Rp 200.000.""")

# Remove stopwords from articles
bloblist = preprocess([article1, article2, article3, article4], stopwords)
# Get word frequency in each article using tfidf
scores_list = []
for i, blob in enumerate(bloblist):
    print("Top words in article {}".format(i + 1))
    scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
    sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)
    for word, score in sorted_words[:3]:
        print("\tWord: {}, TF-IDF: {}".format(word, round(score, 5)))
    scores_list.append(scores)
# Get similarity between articles using cosine similarity
distances_list = []
for i,scores in enumerate(scores_list):
    distances = {}
    for j,scores_tmp in enumerate(scores_list):
        distances[j+1] = 1 - spatial.distance.cosine(scores.values()[:13],scores_tmp.values()[:13])
    distances_sorted = sorted(distances.items(), key=operator.itemgetter(1), reverse=True)
    distances_list.append(distances_sorted)    
# Print the result
for i,distances in enumerate(distances_list):
    print("Article {} : {}".format(i+1, sum(distances[1:], ())))
