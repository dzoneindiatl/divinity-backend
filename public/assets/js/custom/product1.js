$('[name=meta_keywords]').tagify();

function displaySlug(ele) {
    let $this = ele;
        category = $this.val(),
        lowercaseString = category.toLowerCase(),
        replacedString = lowercaseString.replace(/ /g, '-');

        $('.category-slug').text('Slug : https://jaipurjewelleryhouse.com/' + replacedString);
}

function editDisplaySlug(ele) {
    let $this = ele;
        editCategory = $this.val(),
        editLowercaseString = editCategory.toLowerCase(),
        editReplacedString = editLowercaseString.replace(/ /g, '-');

        $('.edit-category-slug').text('Slug : https://jaipurjewelleryhouse.com/' + editReplacedString);
}