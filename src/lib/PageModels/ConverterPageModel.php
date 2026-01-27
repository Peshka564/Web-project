<?php

namespace PageModels;

use db\DBClient;
use db\models\History;
use db\repository\HistoryRepository;
use db\repository\SessionRepository;
use Emitters\JsonEmitter\JsonEmitter;
use Emitters\JsonEmitter\JsonEmitterConfig;
use Emitters\TomlEmitter\TomlEmitter;
use Emitters\TomlEmitter\TomlEmitterConfig;
use Emitters\YamlEmitter\YamlEmitter;
use Emitters\YamlEmitter\YamlEmitterConfig;
use Exception;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\FailedClassLoadingException;
use Transformer\Evaluator\TransformerContext;

class ConverterPageModel
{
    private HistoryRepository $history;
    private SessionRepository $sessions;
    private int $curr_user_id;
    private string|null $id;
    private string $jsonInput;
    private string $sExpr;
    private string $outputLanguage;

    private string|null $action;
    private string|null $title;
    private string|null $description;

    public function __construct(HistoryRepository $history, SessionRepository $sessions)
    {
        $this->history = $history;
        $this->sessions = $sessions;
        $this->curr_user_id = self::getCurrentUser();
        $this->id = null;
        $this->jsonInput = "null";
        $this->sExpr = "(. id)";
        $this->outputLanguage = "FormattedJson";
        $this->action = null;
        $this->title = "";
        $this->description = "";

        if (array_key_exists("id", $_GET)) {
            $this->id = $_GET['id'];
            $historyRow = $history->findById($this->id);

            if ($this->curr_user_id !== $historyRow->user_id) {
                http_response_code(403);
                exit;
            }

            $this->jsonInput = $historyRow->input_data_path;
            $this->sExpr = $historyRow->s_expression_path;
            $this->title = $historyRow->name;
            $this->description = $historyRow->description;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (array_key_exists('converter-input', $_POST)) {
                $this->jsonInput = $_POST['converter-input'];
            }
            if (array_key_exists('s-expr-input', $_POST) && $_POST['s-expr-input'] !== "") {
                $this->sExpr = $_POST['s-expr-input'];
            }
            if (array_key_exists('output-language', $_POST)) {
                $this->outputLanguage = $_POST['output-language'];
            }
            if (array_key_exists('action', $_POST)) {
                $this->action = $_POST['action'];
            }
            if (array_key_exists('save-title', $_POST)) {
                $this->title = $_POST['save-title'];
            }
            if (array_key_exists('save-description', $_POST)) {
                $this->description = $_POST['save-description'];
            }
        }
    }

    private function getCurrentUser()
    {
        return $this->sessions->findByToken($_SESSION["auth_token"])->user_id;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getJsonInput(): string
    {
        return $this->jsonInput;
    }

    public function getSExpr(): string
    {
        return $this->sExpr;
    }

    public function getOutputLanguage(): string
    {
        return $this->outputLanguage;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }



    public function convert(): string
    {
        try {
            $ctx = new TransformerContext(TRANSFORMER_STDLIB_PATH, TRANSFORMER_PLUGINS_PATH);

            $parser = new Parser(new Lexer($this->jsonInput, JSON_PARSER_LEXER_TAB_COLMS));
            $parserResult = $parser->parse();
            if ($parserResult->isErr()) {
                return $parserResult->err()->__toString();
            } else {
                $node = $parserResult->ok();
                $transformedNode = Evaluator::eval($this->sExpr, $node, $ctx);
                switch ($this->outputLanguage) {
                    case "FormattedJson":
                        $opt = new JsonEmitterConfig(
                            EMITTERS_JSON_EMITTERS_INDENTATION_STRING,
                            EMITTERS_JSON_EMITTERS_NEWLINE_STRING
                        );
                        return new JsonEmitter($opt)->emit($transformedNode);
                    case "YAML":
                        $opt = new YamlEmitterConfig(
                            EMITTERS_YAML_EMITTERS_INDENTATION_STRING,
                            EMITTERS_YAML_EMITTERS_NEWLINE_STRING
                        );
                        return new YamlEmitter($opt)->emit($transformedNode);
                    case "TOML":
                        $opt = new TomlEmitterConfig(
                            EMITTERS_TOML_EMITTERS_INDENTATION_STRING,
                            EMITTERS_TOML_EMITTERS_NEWLINE_STRING
                        );
                        return new TomlEmitter($opt)->emit($transformedNode);
                    default:
                        return "Unsupported output language";
                }
            }
        } catch (FailedClassLoadingException $e) {
            return $e->getMessage();
        } catch (EvaluationException $e) {
            return $e->getMessage();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    public function isSaveAction()
    {
        return $this->action === "save";
    }

    public function saveHistory()
    {

        $history = new History();
        $history->user_id = $this->curr_user_id;
        $history->input_data_path = $this->jsonInput;
        $history->s_expression_path = $this->sExpr;
        $history->name = $this->title;
        $history->description = $this->description;

        if ($this->id === null) {
            $history = $this->history->create($history);
            $this->id = $history->id;
        } else {
            $history->id = $this->id;
            $this->history->update($this->id, $history);
        }
    }
}