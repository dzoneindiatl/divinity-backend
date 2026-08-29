$('[name=meta_keywords]').tagify();

function displaySlug(ele) {
    let $this = ele;
        subCategory = $this.val(),
        lowercaseString = subCategory.toLowerCase(),
        replacedString = lowercaseString.replace(/ /g, '-');

        $('.sub-category-slug').text('Slug : /' + replacedString);
} 

function editDisplaySlug(ele) {
    let $this = ele;
        editSubCategory = $this.val(),
        editLowercaseString = editSubCategory.toLowerCase(),
        editReplacedString = editLowercaseString.replace(/ /g, '-');

        $('.edit-sub-category-slug').text('Slug : /' + editReplacedString);
}