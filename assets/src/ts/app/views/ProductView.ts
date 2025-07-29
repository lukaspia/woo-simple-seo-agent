import $ from 'jquery';

export class ProductView {
    private title: JQuery;
    private keywords: JQuery;

    constructor() {
        this.title = $('#title');
        this.keywords = $('.tagchecklist');
    }

    public updateProductMeta(type: string, value: string): void {
        const updateMethods = {
            'title': () => this.updateTitle(value),
            'description': () => this.updateDescription(value),
            'short_description': () => this.updateShortDescription(value),
            'keywords': () => this.updateKeywords(value),
        };

        const updateMethod = updateMethods[type as keyof typeof updateMethods];
        if (updateMethod) {
            updateMethod();
        } else {
            console.warn(`No update method found for type: ${type}`);
        }
    }

    public updateTitle(title: string): void {
        this.title.val(title);
    }

    public updateDescription(description: string): void {
        const iframeElement = $('#content_ifr')[0] as HTMLIFrameElement;

        this.updateIframeContent(iframeElement, description);
    }

    public updateShortDescription(shortDescription: string): void {
        const iframeElement = $('#excerpt_ifr')[0] as HTMLIFrameElement;

        this.updateIframeContent(iframeElement, shortDescription);
    }

    public updateKeywords(keywords: string): void {
        const keywordsArray = keywords.split(',').map(keyword => keyword.trim());

        this.keywords.empty();

        const htmlElements = keywordsArray.map((keyword, index) => {
            const uniqueId = `product_tag-check-num-${index}`;
            return `<li>
                <button type="button" id="${uniqueId}" class="ntdelbutton">
                    <span class="remove-tag-icon" aria-hidden="true"></span>
                    <span class="screen-reader-text">Usuń: ${keyword}</span>
                </button>&nbsp;${keyword}
            </li>`;
        });

        this.keywords.append(htmlElements.join(''));
    }

    private updateIframeContent(iframeElement: HTMLIFrameElement, content: string): void {
        if (iframeElement && iframeElement.contentWindow && iframeElement.contentWindow.document) {
            const iframeContentWindow = iframeElement.contentWindow;
            const iframeDoc = iframeContentWindow.document;
            $(iframeDoc).find('body#tinymce p:first-of-type').text(content);
        } else {
            console.warn('Could not find iframe element or iframe content window.');
        }
    }
}