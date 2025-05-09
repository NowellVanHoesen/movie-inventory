<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $initial_certification = [
        ['name' => 'NR', 'meaning' => 'No rating information.', 'order' => 0],
        ['name' => 'G', 'meaning' => 'All ages admitted. There is no content that would be objectionable to most parents. This is one of only two ratings dating back to 1968 that still exists today.', 'order' => 1],
        ['name' => 'PG', 'meaning' => 'Some material may not be suitable for children under 10. These films may contain some mild language, crude/suggestive humor, scary moments and/or violence. No drug content is present. There are a few exceptions to this rule. A few racial insults may also be heard.', 'order' => 2],
        ['name' => 'PG-13', 'meaning' => 'Some material may be inappropriate for children under 13. Films given this rating may contain sexual content, brief or partial nudity, some strong language and innuendo, humor, mature themes, political themes, terror and/or intense action violence. However, bloodshed is rarely present. This is the minimum rating at which drug content is present.', 'order' => 3],
        ['name' => 'R', 'meaning' => 'Under 17 requires accompanying parent or adult guardian 21 or older. The parent/guardian is required to stay with the child under 17 through the entire movie, even if the parent gives the child/teenager permission to see the film alone. These films may contain strong profanity, graphic sexuality, nudity, strong violence, horror, gore, and strong drug use. A movie rated R for profanity often has more severe or frequent language than the PG-13 rating would permit. An R-rated movie may have more blood, gore, drug use, nudity, or graphic sexuality than a PG-13 movie would admit.', 'order' => 4],
        ['name' => 'NC-17', 'meaning' => 'These films contain excessive graphic violence, intense or explicit sex, depraved, abhorrent behavior, explicit drug abuse, strong language, explicit nudity, or any other elements which, at present, most parents would consider too strong and therefore off-limits for viewing by their children and teens. NC-17 does not necessarily mean obscene or pornographic in the oft-accepted or legal meaning of those words.', 'order' => 5],
        ['name' => 'TV-Y', 'meaning' => 'This program is designed to be appropriate for all children.', 'order' => 6],
        ['name' => 'TV-Y7', 'meaning' => 'This program is designed for children age 7 and above.', 'order' => 7],
        ['name' => 'TV-G', 'meaning' => 'Most parents would find this program suitable for all ages.', 'order' => 8],
        ['name' => 'TV-PG', 'meaning' => 'This program contains material that parents may find unsuitable for younger children.', 'order' => 9],
        ['name' => 'TV-14', 'meaning' => 'This program contains some material that many parents would find unsuitable for children under 14 years of age.', 'order' => 10],
        ['name' => 'TV-MA', 'meaning' => 'This program is specifically designed to be viewed by adults and therefore may be unsuitable for children under 17.', 'order' => 11],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->id();
            $table->string('name')->unique();
            $table->text('meaning');
            $table->integer('order')->unsigned();
        });

        DB::table('certifications')->insert($this->initial_certification);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
