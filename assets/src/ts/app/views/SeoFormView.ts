import $ from 'jquery';
import { SeoData } from '../../types';

export class SeoFormView {
    private sendButton = $('#wssa-send-button');
    private answerContainer = $('#wssa-agent-answer');
    private consoleTextarea = $('#wssa_agent_console');
    private checkboxes = $('input[type="checkbox"][name^="wssa_seo_"]');

    public getFormData(): string {
        const requests: string[] = [];
        this.checkboxes.filter(':checked').each(function() {
            const label = $(`label[for='${this.id}']`).text();
            if (label) {
                requests.push(label.trim().toLowerCase());
            }
        });

        let requestMessage = requests.join(', ');
        const additionalInfo = this.consoleTextarea.val();

        if (typeof additionalInfo === 'string' && additionalInfo.trim() !== '') {
            requestMessage += (requestMessage ? '. ' : '') + `Additional info: ${additionalInfo.trim()}`;
        }

        return requestMessage;
    }

    public renderResults(seoData: SeoData): void {
        this.removeLoadingIndicator();

        if (!this.answerContainer.is(':empty')) {
            this.answerContainer.append('<hr style="height: 3px; background-color: #ccd0d4; border: none; margin: 16px 0;">');
        }

        let html = '<div>';
        html += '<h4>SEO Suggestions</h4>';
        html += `<p><strong>Title:</strong> ${seoData.title || 'N/A'}</p>`;
        html += `<p><strong>Description:</strong> ${seoData.description || 'N/A'}</p>`;
        html += `<p><strong>Short Description:</strong> ${seoData.shortDescription || 'N/A'}</p>`;
        html += `<p><strong>Keywords:</strong> ${seoData.keywords || 'N/A'}</p>`;
        html += '<hr style="height: 1px; background-color: #ccd0d4; border: none; margin: 16px 0;">';
        html += '<h4>Summary</h4>';
        html += `<p>${seoData.summary || 'No summary provided.'}</p>`;
        html += '</div>';

        this.answerContainer.append(html);
        this.scrollToBottom();
    }

    public renderError(message: string): void {
        this.removeLoadingIndicator();

        if (!this.answerContainer.is(':empty')) {
            this.answerContainer.append('<hr style="height: 1px; background-color: #ccd0d4; border: none; margin: 16px 0;">');
        }

        this.answerContainer.append(`<div style="color: red;"><strong>Error:</strong> ${message}</div>`);
        this.scrollToBottom();
    }

    public toggleLoading(isLoading: boolean): void {
        this.sendButton.prop('disabled', isLoading);
        if (isLoading) {
            const loadingHtml = '<p class="wssa-loading-indicator"><em>Generating SEO data, please wait...</em></p>';
            this.answerContainer.append(loadingHtml);
            this.scrollToBottom();
        }
    }

    public onSendClick(callback: () => void): void {
        this.sendButton.on('click', callback);
    }

    private removeLoadingIndicator(): void {
        this.answerContainer.find('.wssa-loading-indicator').remove();
    }

    private scrollToBottom(): void {
        this.answerContainer.scrollTop(this.answerContainer[0].scrollHeight);
    }
}
