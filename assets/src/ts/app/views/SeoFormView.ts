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

        let suggestionsHtml = '';
        if (seoData.title) {
            suggestionsHtml += `<p><strong>Title:</strong> ${seoData.title} ${this.getAcceptButton()}</p>`;
        }
        if (seoData.description) {
            suggestionsHtml += `<p><strong>Description:</strong> ${seoData.description} ${this.getAcceptButton()}</p>`;
        }
        if (seoData.shortDescription) {
            suggestionsHtml += `<p><strong>Short Description:</strong> ${seoData.shortDescription} ${this.getAcceptButton()}</p>`;
        }
        if (seoData.keywords) {
            suggestionsHtml += `<p><strong>Keywords:</strong> ${seoData.keywords} ${this.getAcceptButton()}</p>`;
        }

        let html = '<div>';
        if (suggestionsHtml) {
            html += '<h4>SEO Suggestions</h4>';
            html += suggestionsHtml;
            html += '<div class="button" style="display: inline-flex; align-items: center; text-align: center; gap: 5px">Accept all<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-check" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/></svg></div>';
            html += '<hr style="height: 1px; background-color: #ccd0d4; border: none; margin: 16px 0;">';
        }

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

    private getAcceptButton(): string {
        return '<div class="button" style="display: inline-flex; align-items: center; gap: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg></div>';
    }
}
